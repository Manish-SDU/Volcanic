<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;

return function () {
    // Authentication Routes
    Route::middleware('guest')->group(function () {
        // Registration
        Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

        // Login (POST only is guest-restricted)
        Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');

        // Password reset (guest)
        Route::get('/password/reset', [PasswordController::class, 'showReset'])->name('password.reset.show');
        Route::post('/password/reset', [PasswordController::class, 'reset'])->name('password.reset');
    });

    // Login (GET) – accessible to everyone (matches original behavior)
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    // Logout (only accessible when authenticated)
    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // Password change (auth)
    Route::get('/password/change', [PasswordController::class, 'showChange'])
        ->middleware('auth')
        ->name('password.change.show');
    Route::post('/password/change', [PasswordController::class, 'change'])
        ->middleware('auth')
        ->name('password.change');
};
