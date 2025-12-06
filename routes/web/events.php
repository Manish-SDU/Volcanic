<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VolcanoRealtimeController;

return function () {
    // Public page for the real-time volcano activity
    Route::get('/volcano-activity', [VolcanoRealtimeController::class, 'index'])
        ->name('volcano.realtime');
};
