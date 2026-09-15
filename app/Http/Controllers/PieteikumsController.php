<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PieteikumsController extends Controller
{
    public function create(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('form', [
            'brugaVeidi' => BrugaVeids::orderBy('name')->get(),
            'pieteikumi' => Pieteikums::where('user_id', Auth::id())
                ->with(['pavingType', 'review'])
                ->latest()
                ->get(),
        ]);
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
            'project_description' => ['required', 'string', 'max:5000'],
        ], [
            'client_name.required' => 'Lūdzu, ievadiet savu vārdu.',
            'client_email.required' => 'Lūdzu, ievadiet e-pasta adresi.',
            'client_email.email' => 'E-pasta adrese nav derīga.',
            'client_phone.required' => 'Lūdzu, ievadiet telefona numuru.',
            'project_description.required' => 'Lūdzu, aprakstiet savu projektu.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'new';

        Pieteikums::create($validated);

        return redirect()->route('form')->withInput()->with('success', 'Pieteikums nosūtīts! Sazināsimies ar jums tuvākajā laikā.');
    }
}