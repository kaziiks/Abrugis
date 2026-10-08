<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\PavingType;
use App\Models\PortfolioImage;
use App\Models\PortfolioInfo;
use App\Services\ApplicationReservationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $calendarMonth = Carbon::createFromFormat('!Y-m', $request->string('calendar_month')->toString() ?: now()->format('Y-m'));
        } catch (\Throwable) {
            $calendarMonth = now()->startOfMonth();
        }

        $calendarMonth->startOfMonth();
        $calendarApplications = Application::with('pavingType')
            ->whereNotNull('requested_date')
            ->where('status', '!=', 'rejected')
            ->whereBetween('requested_date', [$calendarMonth->copy()->startOfMonth(), $calendarMonth->copy()->endOfMonth()])
            ->orderBy('requested_date')
            ->get()
            ->groupBy(fn (Application $application): string => $application->requested_date->format('Y-m-d'));

        return view('admin.dashboard', [
            'adminSection' => 'calendar',
            'calendarMonth' => $calendarMonth,
            'calendarApplications' => $calendarApplications,
        ]);
    }

    public function applications(Request $request): View
    {
        $requestedDate = $request->validate([
            'requested_date' => ['sometimes', 'date_format:Y-m-d'],
        ])['requested_date'] ?? null;

        $applications = Application::with(['user', 'pavingType'])->latest();

        if ($requestedDate) {
            $applications->whereDate('requested_date', $requestedDate);
        }

        return view('admin.dashboard', [
            'adminSection' => 'applications',
            'applications' => $applications->get(),
            'requestedDate' => $requestedDate,
        ]);
    }

    public function portfolio(): View
    {
        return view('admin.dashboard', [
            'adminSection' => 'portfolio',
            'portfolio' => PortfolioInfo::with(['user', 'pavingType', 'images'])->latest()->get(),
            'pavingTypes' => PavingType::orderBy('name')->get(),
        ]);
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $this->validatePortfolio($request);
        $validated['user_id'] = Auth::id();

        $storedPaths = [];
        try {
            DB::transaction(function () use ($request, $validated, &$storedPaths): void {
                $portfolio = PortfolioInfo::create($validated);
                $storedPaths = $this->storePortfolioImages($request, $portfolio);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredImages($storedPaths);
            throw $exception;
        }

        return back()->with('success', 'Portfolio projekts pievienots.');
    }

    public function updatePortfolio(Request $request, PortfolioInfo $portfolio): RedirectResponse
    {
        $validated = $this->validatePortfolio($request);
        $storedPaths = [];
        try {
            DB::transaction(function () use ($request, $portfolio, $validated, &$storedPaths): void {
                $portfolio->update($validated);
                $storedPaths = $this->storePortfolioImages($request, $portfolio);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredImages($storedPaths);
            throw $exception;
        }

        return back()->with('success', 'Portfolio projekts atjaunināts.');
    }

    public function destroyPortfolio(PortfolioInfo $portfolio): RedirectResponse
    {
        foreach ($portfolio->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio projekts izdzēsts.');
    }

    public function destroyPortfolioImage(PortfolioImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Portfolio bilde izdzēsta.');
    }

    public function updateApplication(Request $request, Application $application, ApplicationReservationService $reservations): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,approved,completed,rejected'],
        ], [
            'status.required' => 'Lūdzu, izvēlieties pieteikuma statusu.',
            'status.in' => 'Statuss nav derīgs.',
        ]);

        $reservations->updateStatus($application, $validated['status']);

        return back()->with('success', 'Pieteikums atjaunināts.');
    }

    private function validatePortfolio(Request $request): array
    {
        return $request->validate([
            'paving_type_id' => ['required', 'exists:paving_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'city' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'completed_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'paving_type_id.required' => 'Lūdzu, izvēlieties bruģa veidu.',
            'title.required' => 'Lūdzu, ievadiet projekta nosaukumu.',
            'city.required' => 'Lūdzu, ievadiet pilsētu.',
        ]);
    }

    private function storePortfolioImages(Request $request, PortfolioInfo $portfolio): array
    {
        $paths = [];
        try {
            foreach ($request->file('images', []) as $image) {
                $path = $image->store('portfolio', 'public');
                if ($path === false) {
                    throw new RuntimeException('Portfolio image could not be stored.');
                }

                $paths[] = $path;
            }

            foreach ($paths as $path) {
                $portfolio->images()->create(['image_path' => $path]);
            }
        } catch (Throwable $exception) {
            $this->deleteStoredImages($paths);
            throw $exception;
        }

        return $paths;
    }

    private function deleteStoredImages(array $paths): void
    {
        if ($paths !== [] && ! Storage::disk('public')->delete($paths)) {
            Log::error('Portfolio image cleanup failed after an unsuccessful operation.', [
                'image_paths' => $paths,
            ]);
        }
    }
}