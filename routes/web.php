<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolcanoesController;
use App\Models\Volcano;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Home Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Login Routes
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.process');

// Registration Routes (placeholders for future implementation)
Route::get('/register', function() {
    return redirect()->route('login');
})->name('register');

// Password Reset Routes (placeholders for future implementation)
Route::get('/forgot-password', function() {
    return redirect()->route('login');
})->name('password.request');

// Profile Routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

// My Volcanoes Routes
Route::get('/my-volcanoes', [VolcanoesController::class, 'index'])->name('my-volcanoes');

// Schema route (for development only)
Route::get('/schema', [App\Http\Controllers\SchemaController::class, 'showSchema']);

// API routes for volcanoes
Route::get('/api/volcanoes', function() {
    $volcanoes = App\Models\Volcano::select('id', 'name', 'country', 'region', 'activity', 'type', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
        ->orderBy('name')
        ->get();
        
    return response()->json([
        'success' => true,
        'data' => $volcanoes,
        'count' => $volcanoes->count()
    ]);
});
Route::get('/api/volcanoes/search', [App\Http\Controllers\VolcanoController::class, 'search']);

// Debug route
Route::get('/debug/image-paths', function () {
    $volcanoes = Volcano::all();
    $paths = [];
    
    foreach ($volcanoes as $volcano) {
        $baseName = strtolower(str_replace(' ', '_', $volcano->name));
        $paths[] = [
            'volcano_name' => $volcano->name,
            'base_name' => $baseName,
            'safe_image_url' => $volcano->safe_image_url,
            'file_exists' => file_exists(public_path("images/volcanoes/{$baseName}.png"))
        ];
    }
    
    return response()->json($paths);
});
