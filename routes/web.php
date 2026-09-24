<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AtsauksmeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PieteikumsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['lv', 'en', 'ru'], true), 404);

    session(['locale' => $locale]);

    return back();
})->name('language');

Route::get('/calc', [CalculatorController::class, 'index'])->name('calc');
Route::post('/calc', [CalculatorController::class, 'calculate'])->name('calc.calculate');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/form', [PieteikumsController::class, 'create'])->name('form');
    Route::get('/applications', [PieteikumsController::class, 'applications'])->name('applications');
    Route::get('/calendar', [PieteikumsController::class, 'calendar'])->name('calendar');
    Route::post('/form', [PieteikumsController::class, 'store'])->middleware('throttle:5,1')->name('form.store');
    Route::post('/atsauksmes', [AtsauksmeController::class, 'store'])->middleware('throttle:5,1')->name('atsauksmes.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
    Route::get('/portfolio', [AdminController::class, 'portfolio'])->name('portfolio');
    Route::post('/portfolio', [AdminController::class, 'storePortfolio'])->name('portfolio.store');
    Route::put('/portfolio/{portfolio}', [AdminController::class, 'updatePortfolio'])->name('portfolio.update');
    Route::delete('/portfolio/{portfolio}', [AdminController::class, 'destroyPortfolio'])->name('portfolio.destroy');
    Route::delete('/portfolio-bildes/{bilde}', [AdminController::class, 'destroyPortfolioBilde'])->name('portfolio.bildes.destroy');
    Route::patch('/pieteikumi/{pieteikums}', [AdminController::class, 'updatePieteikums'])->name('pieteikumi.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
