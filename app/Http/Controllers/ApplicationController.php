<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use App\Models\Application;
use App\Models\PavingType;
use App\Services\ApplicationReservationService;
use App\Services\CalculatorEstimateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function create(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('form', [
            'pavingTypes' => PavingType::orderBy('name')->get(),
            'estimate' => session('calculator_estimate'),
        ]);
    }

    public function applications(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('applications', [
            'applications' => Application::where('user_id', Auth::id())
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
        $bookedDates = Application::query()
            ->whereNotNull('requested_date')
            ->whereIn('status', ['approved', 'completed'])
            ->whereBetween('requested_date', [$calendarMonth->copy()->startOfMonth(), $calendarMonth->copy()->endOfMonth()])
            ->pluck('requested_date')
            ->map(fn ($date): string => Carbon::parse($date)->format('Y-m-d'))
            ->unique();

        return view('calendar', compact('calendarMonth', 'bookedDates'));
    }

    public function store(
        Request $request,
        ApplicationReservationService $reservations,
        CalculatorEstimateService $estimates,
    ): RedirectResponse {
        abort_if(Auth::user()?->role === 'admin', 403);

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'paving_type_id' => ['nullable', 'exists:paving_types,id'],
            'area_m2' => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'requested_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
                Rule::unique('applications', 'requested_date')
                    ->where(fn ($query) => $query->whereIn('status', ['approved', 'completed'])),
            ],
            'project_description' => ['required', 'string', 'max:5000'],
        ], [
            'client_name.required' => 'Lūdzu, ievadiet savu vārdu.',
            'client_email.required' => 'Lūdzu, ievadiet e-pasta adresi.',
            'client_email.email' => 'E-pasta adrese nav derīga.',
            'client_phone.required' => 'Lūdzu, ievadiet telefona numuru.',
            'project_description.required' => 'Lūdzu, aprakstiet savu projektu.',
            'requested_date.unique' => 'Šis konsultācijas datums jau ir apstiprināts. Lūdzu, izvēlieties citu datumu.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['client_email'] = Auth::user()->email;
        $validated['status'] = 'new';

        $estimate = $request->session()->get('calculator_estimate');
        if (is_array($estimate)) {
            $estimate = $estimates->calculate([
                'area' => $estimate['area'],
                'paving_id' => $estimate['paving_type_id'],
                'base' => $estimate['base'],
                'removal' => $estimate['removal'],
            ]);
            $validated['estimate_total'] = $estimate['total'];
            $validated['estimate_details'] = $estimate;
            $validated['area_m2'] = $estimate['area'];
            $validated['paving_type_id'] = $estimate['paving_type_id'];
        }

        $application = $reservations->create($validated)->load('pavingType');
        $request->session()->forget('calculator_estimate');

        try {
            Mail::to($application->client_email)->send(new ApplicationSubmitted($application));
            Mail::to(config('mail.admin_address'))->send(new ApplicationSubmitted($application, true));
        } catch (\Throwable $exception) {
            Log::error('Application notification email could not be sent.', [
                'application_id' => $application->id,
                'exception' => $exception,
            ]);
        }

        return redirect()->route('applications')->with('success', 'Pieteikums nosūtīts! Sazināsimies ar jums tuvākajā laikā.');
    }
}
