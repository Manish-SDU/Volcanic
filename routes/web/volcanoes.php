<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VolcanoesController;
use App\Http\Controllers\UserVolcanoController;
use App\Models\Volcano;

return function () {
    // My Volcanoes Routes
    Route::get('/my-volcanoes', [VolcanoesController::class, 'index'])
        ->middleware('auth')
        ->name('my-volcanoes');

    // API routes for volcanoes list
    Route::get('/api/volcanoes', function () {
        try {
            \Log::info('API /api/volcanoes called');

            $volcanoes = \App\Models\Volcano::select(
                    'id',
                    'name',
                    'country',
                    'activity',
                    'type',
                    'elevation',
                    'image_url',
                    'latitude',
                    'longitude',
                    'description'
                )
                ->orderBy('name')
                ->get()
                ->map(function ($volcano) {
                    // Ensure coordinates and elevation are proper numeric types
                    $volcano->latitude = (float) $volcano->latitude;
                    $volcano->longitude = (float) $volcano->longitude;
                    $volcano->elevation = (int) $volcano->elevation;
                    return $volcano;
                });

            \Log::info('Volcanoes retrieved: ' . $volcanoes->count());

            return response()->json([
                'success' => true,
                'data'    => $volcanoes,
                'count'   => $volcanoes->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    })->name('api.volcanoes');

    Route::get('/api/volcanoes/search', [VolcanoesController::class, 'search'])
        ->name('api.volcanoes.search');

    // Individual Volcano API Route
    Route::get('/api/volcanoes/{id}', [VolcanoesController::class, 'getVolcano']);

    // Visited / Wishlist routes – require authentication
    Route::middleware(['auth'])->group(function () {
        // Toggle visited/wishlist status
        Route::post('/user/volcanoes/{id}/{status}', [UserVolcanoController::class, 'toggleStatus'])
            ->name('user.volcanoes.toggle');

        // Get lists for the current user
        Route::get('/user/volcanoes/lists', [UserVolcanoController::class, 'getLists']);

        // Check status for a specific volcano
        Route::get('/user/volcanoes/{id}/status', [UserVolcanoController::class, 'checkStatus']);

        // Update visited date
        Route::put('/user/volcanoes/{volcanoId}/update-date', [UserVolcanoController::class, 'updateVisitedAt'])
            ->name('user.volcanoes.updateDate');
    });
};
