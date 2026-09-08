<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PieteikumsController extends Controller
{
    public function create(): View
    {
        return view('form', [
            'brugaVeidi' => BrugaVeids::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'bruga_veids_id' => ['nullable', 'exists:bruga_veids,id'],
            'area_m2' => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'project_description' => ['required', 'string', 'max:5000'],
        ]);

        Pieteikums::create($validated);

        return redirect()->route('form')->with('success', 'Pieteikums nosūtīts! Sazināsimies ar jums tuvākajā laikā.');
    }
}