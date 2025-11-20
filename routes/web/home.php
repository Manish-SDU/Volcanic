<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

return function () {
    // Home Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
};
