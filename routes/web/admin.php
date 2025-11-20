<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Middleware\IsAdmin;

return function () {
    // Admin Dashboard (only for admins)
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.dashboard');
};
