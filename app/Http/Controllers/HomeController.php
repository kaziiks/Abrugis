<?php

namespace App\Http\Controllers;

use App\Models\BrugaVeids;
use App\Models\Atsauksme;
use App\Models\PortfolioInfo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $selectedPavingTypeId = $request->integer('bruga_veids_id');

        if (! BrugaVeids::whereKey($selectedPavingTypeId)->exists()) {
            $selectedPavingTypeId = null;
        }

        return view('welcome', [
            'portfolio' => PortfolioInfo::with(['bildes', 'brugaVeids'])
                ->when($selectedPavingTypeId, fn ($query) => $query->where('bruga_veids_id', $selectedPavingTypeId))
                ->latest()
                ->get(),
            'brugaVeidi' => BrugaVeids::orderBy('name')->get(),
            'selectedPavingTypeId' => $selectedPavingTypeId,
            'atsauksmes' => Atsauksme::latest()->take(6)->get(),
        ]);
    }
}
