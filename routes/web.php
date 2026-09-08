<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PieteikumsController;
use App\Models\PortfolioInfo;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'portfolio' => PortfolioInfo::latest()->get(),
    ]);
});

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
