<?php

namespace App\Http\Controllers;

use App\Models\PavingType;
use App\Services\CalculatorEstimateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function index(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('calc', [
            'pavingTypes' => PavingType::orderBy('price_per_m2')->get(),
            'baseOptions' => config('abrugis.calculator.base_options'),
            'removalPrice' => config('abrugis.calculator.removal_price_per_m2'),
        ]);
    }

    public function calculate(Request $request, CalculatorEstimateService $estimates): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        $validated = $request->validate([
            'area' => ['required', 'numeric', 'min:1', 'max:'.config('abrugis.calculator.maximum_area_m2')],
            'paving_id' => ['required', 'integer', 'exists:paving_types,id'],
            'base' => ['required', Rule::in(array_keys(config('abrugis.calculator.base_options')))],
            'removal' => ['nullable', 'boolean'],
        ]);

        $estimate = $estimates->calculate([
            ...$validated,
            'removal' => $request->boolean('removal'),
        ]);
        $request->session()->put('calculator_estimate', $estimate);
        $pavingType = PavingType::findOrFail($validated['paving_id']);

        return view('calc', [
            'pavingTypes' => PavingType::orderBy('price_per_m2')->get(),
            'baseOptions' => config('abrugis.calculator.base_options'),
            'removalPrice' => config('abrugis.calculator.removal_price_per_m2'),
            'selectedPavingId' => $pavingType->id,
            'selectedBase' => $validated['base'],
            'removalSelected' => $request->boolean('removal'),
            'selectedPavingName' => $estimate['paving_name'],
            'estimate' => $estimate,
        ]);
    }
}
