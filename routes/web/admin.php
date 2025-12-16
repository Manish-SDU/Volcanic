<?php

use App\Http\Controllers\Admin\AchievementsController;
use App\Http\Controllers\Admin\UsersAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VolcanoesAdminController;
use App\Http\Controllers\Admin\AchievementsAdminController;
use App\Http\Middleware\IsAdmin;

return function () {
    // Admin Dashboard (only for admins)

    // Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    //     ->middleware(['auth', IsAdmin::class])
    //     ->name('admin.dashboard');

    Route::get('/admin/dashboard', function() {
        return redirect()->route('admin.manage-volcanoes');
    })
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.dashboard');

    Route::get('/admin/manage-volcanoes', [VolcanoesAdminController::class, 'index'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-volcanoes');

    Route::post('/admin/manage-volcanoes', [VolcanoesAdminController::class, 'store'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-volcanoes.store');

    Route::delete('/admin/manage-volcanoes/{id}', [VolcanoesAdminController::class, 'destroy'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-volcanoes.destroy');

    Route::get('/admin/manage-achievements', [AchievementsAdminController::class, 'index'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-achievements');

    Route::post('/admin/manage-achievements', [AchievementsAdminController::class, 'store'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-achievements.store');

    Route::delete('/admin/manage-achievements/{id}', [AchievementsAdminController::class, 'destroy'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-achievements.destroy');

    Route::get('/admin/manage-users', [UsersAdminController::class, 'index'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-users');

    Route::delete('/admin/manage-users/{id}', [UsersAdminController::class, 'destroy'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.manage-users.destroy');
};
