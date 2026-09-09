<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PieteikumsController;
use App\Models\BrugaVeids;
use App\Models\PortfolioInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'portfolio' => PortfolioInfo::latest()->get(),
    ]);
});

Route::get('/calc', function () {
    return view('calc', [
        'brugaVeidi' => BrugaVeids::orderBy('price_per_m2')->get(),
    ]);
})->name('calc');

Route::post('/calc', function (Request $request) {
    $validated = $request->validate([
        'area' => ['required', 'numeric', 'min:1', 'max:100000'],
        'paving' => ['required', 'numeric', 'min:0'],
        'base' => ['required', 'numeric', 'in:18,28'],
        'removal' => ['nullable', 'numeric', 'in:8'],
    ]);

    $area = (float) $validated['area'];
    $paving = (float) $validated['paving'];
    $base = (float) $validated['base'];
    $removal = (float) ($validated['removal'] ?? 0);
    $pavingType = BrugaVeids::where('price_per_m2', $paving)->first();

    return view('calc', [
        'brugaVeidi' => BrugaVeids::orderBy('price_per_m2')->get(),
        'selectedPavingName' => $pavingType?->name ?? 'Bruģis',
        'estimate' => [
            'total' => $area * ($paving + $base + $removal),
            'paving' => $area * $paving,
            'base' => $area * $base,
            'removal' => $area * $removal,
        ],
    ]);
})->name('calc.calculate');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/form', [PieteikumsController::class, 'create'])->name('form');
    Route::post('/form', [PieteikumsController::class, 'store'])->name('form.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
