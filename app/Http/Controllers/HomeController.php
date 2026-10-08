<?php

namespace App\Http\Controllers;

use App\Models\PavingType;
use App\Models\PortfolioInfo;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $selectedPavingTypeId = $request->integer('paving_type_id');

        if (! PavingType::whereKey($selectedPavingTypeId)->exists()) {
            $selectedPavingTypeId = null;
        }

        $portfolio = PortfolioInfo::with(['images', 'pavingType'])
            ->when($selectedPavingTypeId, fn ($query) => $query->where('paving_type_id', $selectedPavingTypeId))
            ->latest()
            ->get();

        $portfolio->each(function (PortfolioInfo $project): void {
            $project->setRelation(
                'images',
                $project->images->filter(
                    fn ($image): bool => Storage::disk('public')->exists($image->image_path),
                )->values(),
            );
        });

        return view('welcome', [
            'portfolio' => $portfolio,
            'pavingTypes' => PavingType::orderBy('name')->get(),
            'selectedPavingTypeId' => $selectedPavingTypeId,
            'reviews' => Review::whereHas('application', fn ($query) => $query->where('status', 'completed'))
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}
