<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PieteikumsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/calc', [CalculatorController::class, 'index'])->name('calc');
Route::post('/calc', [CalculatorController::class, 'calculate'])->name('calc.calculate');

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/portfolio', [AdminController::class, 'storePortfolio'])->name('portfolio.store');
    Route::put('/portfolio/{portfolio}', [AdminController::class, 'updatePortfolio'])->name('portfolio.update');
    Route::delete('/portfolio/{portfolio}', [AdminController::class, 'destroyPortfolio'])->name('portfolio.destroy');
    Route::patch('/pieteikumi/{pieteikums}', [AdminController::class, 'updatePieteikums'])->name('pieteikumi.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
