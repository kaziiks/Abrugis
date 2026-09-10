<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use App\Models\PortfolioInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'portfolio' => PortfolioInfo::latest()->get(),
            'pieteikumi' => Pieteikums::with('pavingType')->latest()->get(),
            'brugaVeidi' => BrugaVeids::orderBy('name')->get(),
        ]);
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $this->validatePortfolio($request);
        PortfolioInfo::create($validated);

        return back()->with('success', 'Portfolio projekts pievienots.');
    }

    public function updatePortfolio(Request $request, PortfolioInfo $portfolio): RedirectResponse
    {
        $portfolio->update($this->validatePortfolio($request));

        return back()->with('success', 'Portfolio projekts atjaunināts.');
    }

    public function destroyPortfolio(PortfolioInfo $portfolio): RedirectResponse
    {
        $portfolio->delete();

        return back()->with('success', 'Portfolio projekts izdzēsts.');
    }

    public function updatePieteikums(Request $request, Pieteikums $pieteikums): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,approved,completed,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $pieteikums->update($validated);

        return back()->with('success', 'Pieteikums atjaunināts.');
    }

    private function validatePortfolio(Request $request): array
    {
        return $request->validate([
            'bruga_veids_id' => ['required', 'exists:bruga_veids,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'city' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'completed_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);
    }
}