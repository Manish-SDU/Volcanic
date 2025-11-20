<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

return function () {
    // Profile Routes (require authentication)
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        // Edit Profile Routes
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
};
