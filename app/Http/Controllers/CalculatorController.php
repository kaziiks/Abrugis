<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function index(): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        return view('calc', [
            'brugaVeidi' => BrugaVeids::orderBy('price_per_m2')->get(),
        ]);
    }

    public function calculate(Request $request): View
    {
        abort_if(Auth::user()?->role === 'admin', 403);

        $validated = $request->validate([
            'area' => ['required', 'numeric', 'min:1', 'max:100000'],
            'paving' => ['required', 'numeric', 'min:0'],
            'base' => ['required', 'numeric', 'in:18,28'],
            'removal' => ['nullable', 'numeric', 'in:8'],
        ]);

        $estimate = $this->buildEstimate($validated);
        $pavingType = BrugaVeids::where('price_per_m2', $validated['paving'])->first();

        return view('calc', [
            'brugaVeidi' => BrugaVeids::orderBy('price_per_m2')->get(),
            'selectedPavingName' => $pavingType?->name ?? 'Bruģis',
            'estimate' => $estimate,
        ]);
    }

    private function buildEstimate(array $validated): array
    {
        $area = (float) $validated['area'];
        $paving = (float) $validated['paving'];
        $base = (float) $validated['base'];
        $removal = (float) ($validated['removal'] ?? 0);

        return [
            'total' => $area * ($paving + $base + $removal),
            'paving' => $area * $paving,
            'base' => $area * $base,
            'removal' => $area * $removal,
        ];
    }
}
