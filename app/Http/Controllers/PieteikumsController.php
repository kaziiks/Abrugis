<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PieteikumsController extends Controller
{
    public function create(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('form', [
            'brugaVeidi' => BrugaVeids::orderBy('name')->get(),
        ]);
    }

    public function applications(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('applications', [
            'pieteikumi' => Pieteikums::where('user_id', Auth::id())
                ->with(['pavingType', 'review'])
                ->latest()
                ->get(),
        ]);
    }

    public function calendar(Request $request): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        try {
            $calendarMonth = Carbon::createFromFormat('!Y-m', $request->string('calendar_month')->toString() ?: now()->format('Y-m'));
        } catch (\Throwable) {
            $calendarMonth = now()->startOfMonth();
        }

        $calendarMonth->startOfMonth();
        $bookedDates = Pieteikums::query()
            ->whereNotNull('requested_date')
            ->where('status', '!=', 'rejected')
            ->whereBetween('requested_date', [$calendarMonth->copy()->startOfMonth(), $calendarMonth->copy()->endOfMonth()])
            ->pluck('requested_date')
            ->map(fn ($date): string => Carbon::parse($date)->format('Y-m-d'))
            ->unique();

        return view('calendar', compact('calendarMonth', 'bookedDates'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'bruga_veids_id' => ['nullable', 'exists:bruga_veids,id'],
            'area_m2' => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'requested_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
                Rule::unique('pieteikums', 'requested_date')
                    ->where(fn ($query) => $query->where('status', '!=', 'rejected')),
            ],
            'project_description' => ['required', 'string', 'max:5000'],
        ], [
            'client_name.required' => 'Lūdzu, ievadiet savu vārdu.',
            'client_email.required' => 'Lūdzu, ievadiet e-pasta adresi.',
            'client_email.email' => 'E-pasta adrese nav derīga.',
            'client_phone.required' => 'Lūdzu, ievadiet telefona numuru.',
            'project_description.required' => 'Lūdzu, aprakstiet savu projektu.',
            'requested_date.unique' => 'Šis datums jau ir rezervēts. Lūdzu, izvēlieties citu datumu.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'new';

        $reservationLock = null;

        if ($validated['requested_date'] ?? null) {
            $reservationLock = 'abrugis-reservation-' . $validated['requested_date'];
            $lockResult = DB::select('SELECT GET_LOCK(?, 10) AS acquired', [$reservationLock]);

            if ((int) ($lockResult[0]->acquired ?? 0) !== 1) {
                throw ValidationException::withMessages([
                    'requested_date' => 'Rezervācijas datumu nevarēja pārbaudīt. Lūdzu, mēģiniet vēlreiz.',
                ]);
            }
        }

        try {
            if ($validated['requested_date'] ?? null) {
                $dateIsReserved = Pieteikums::where('requested_date', $validated['requested_date'])
                    ->where('status', '!=', 'rejected')
                    ->exists();

                if ($dateIsReserved) {
                    throw ValidationException::withMessages([
                        'requested_date' => 'Šis datums jau ir rezervēts. Lūdzu, izvēlieties citu datumu.',
                    ]);
                }
            }

            $pieteikums = Pieteikums::create($validated);
            $pieteikums->load('pavingType');

            try {
                Mail::to($pieteikums->client_email)->send(new ApplicationSubmitted($pieteikums));
                Mail::to(config('mail.admin_address'))->send(new ApplicationSubmitted($pieteikums, true));
            } catch (\Throwable $exception) {
                Log::error('Application notification email could not be sent.', [
                    'pieteikums_id' => $pieteikums->id,
                    'exception' => $exception,
                ]);
            }
        } finally {
            if ($reservationLock) {
                DB::select('SELECT RELEASE_LOCK(?)', [$reservationLock]);
            }
        }

        return redirect()->route('applications')->with('success', 'Pieteikums nosūtīts! Sazināsimies ar jums tuvākajā laikā.');
    }
}