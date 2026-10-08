<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->intended('/');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::middleware('verified')->group(function () {
        Route::get('/form', [ApplicationController::class, 'create'])->name('form');
        Route::get('/applications', [ApplicationController::class, 'applications'])->name('applications');
        Route::get('/calendar', [ApplicationController::class, 'calendar'])->name('calendar');
        Route::post('/form', [ApplicationController::class, 'store'])->middleware('throttle:5,1')->name('form.store');
        Route::post('/atsauksmes', [ReviewController::class, 'store'])->middleware('throttle:5,1')->name('reviews.store');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
    Route::get('/portfolio', [AdminController::class, 'portfolio'])->name('portfolio');
    Route::post('/portfolio', [AdminController::class, 'storePortfolio'])->name('portfolio.store');
    Route::put('/portfolio/{portfolio}', [AdminController::class, 'updatePortfolio'])->name('portfolio.update');
    Route::delete('/portfolio/{portfolio}', [AdminController::class, 'destroyPortfolio'])->name('portfolio.destroy');
    Route::delete('/portfolio-bildes/{image}', [AdminController::class, 'destroyPortfolioImage'])->name('portfolio.images.destroy');
    Route::patch('/pieteikumi/{application}', [AdminController::class, 'updateApplication'])->name('applications.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
