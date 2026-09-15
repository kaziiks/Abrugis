<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use App\Models\PortfolioBilde;
use App\Models\PortfolioInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'portfolio' => PortfolioInfo::with(['user', 'brugaVeids', 'bildes'])->latest()->get(),
            'pieteikumi' => Pieteikums::with(['user', 'pavingType'])->latest()->get(),
            'brugaVeidi' => BrugaVeids::orderBy('name')->get(),
        ]);
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $this->validatePortfolio($request);
        $validated['user_id'] = Auth::id();

        $portfolio = PortfolioInfo::create($validated);
        $this->storePortfolioImages($request, $portfolio);

        return back()->with('success', 'Portfolio projekts pievienots.');
    }

    public function updatePortfolio(Request $request, PortfolioInfo $portfolio): RedirectResponse
    {
        $portfolio->update($this->validatePortfolio($request));
        $this->storePortfolioImages($request, $portfolio);

        return back()->with('success', 'Portfolio projekts atjaunināts.');
    }

    public function destroyPortfolio(PortfolioInfo $portfolio): RedirectResponse
    {
        foreach ($portfolio->bildes as $bildes) {
            Storage::disk('public')->delete($bildes->image_path);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio projekts izdzēsts.');
    }

    public function destroyPortfolioBilde(PortfolioBilde $bilde): RedirectResponse
    {
        Storage::disk('public')->delete($bilde->image_path);
        $bilde->delete();

        return back()->with('success', 'Portfolio bilde izdzēsta.');
    }

    public function updatePieteikums(Request $request, Pieteikums $pieteikums): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,approved,completed,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'status.required' => 'Lūdzu, izvēlieties pieteikuma statusu.',
            'status.in' => 'Statuss nav derīgs.',
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
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'bruga_veids_id.required' => 'Lūdzu, izvēlieties bruģa veidu.',
            'title.required' => 'Lūdzu, ievadiet projekta nosaukumu.',
            'city.required' => 'Lūdzu, ievadiet pilsētu.',
        ]);
    }

    private function storePortfolioImages(Request $request, PortfolioInfo $portfolio): void
    {
        foreach ($request->file('images', []) as $image) {
            $portfolio->bildes()->create([
                'image_path' => $image->store('portfolio', 'public'),
            ]);
        }
    }
}