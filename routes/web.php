<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolcanoesController;
use App\Http\Controllers\SchemaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Middleware\IsAdmin;
use App\Models\Volcano;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;

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

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Login
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');

// Logout (only accessible when authenticated)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Password Reset Routes (placeholders for future implementation)
Route::get('/forgot-password', function() {
    return redirect()->route('login');
})->name('password.request');

// Profile Routes
Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

// Admin Dashboard (only for admins)
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', IsAdmin::class])
    ->name('admin.dashboard');

// My Volcanoes Routes
Route::get('/my-volcanoes', [VolcanoesController::class, 'index'])
    ->middleware('auth')
    ->name('my-volcanoes');

// Schema route (for development only)
Route::get('/schema', [App\Http\Controllers\SchemaController::class, 'showSchema']);

// API routes for volcanoes
Route::get('/api/volcanoes', function() {
    try {
        \Log::info('API /api/volcanoes called');
        $volcanoes = App\Models\Volcano::select('id', 'name', 'country', 'activity', 'type', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
            ->orderBy('name')
            ->get()
            ->map(function($volcano) {
                // Ensure coordinates are numbers
                $volcano->latitude = (float) $volcano->latitude;
                $volcano->longitude = (float) $volcano->longitude;
                $volcano->elevation = (int) $volcano->elevation;
                return $volcano;
            });
        
        \Log::info('Volcanoes retrieved: ' . $volcanoes->count());
        
        return response()->json([
            'success' => true,
            'data' => $volcanoes,
            'count' => $volcanoes->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('API Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->name('api.volcanoes');
Route::get('/api/volcanoes/search', [VolcanoesController::class, 'search'])
    ->name('api.volcanoes.search');

//Visited/WishList
Route::get('/api/volcanoes/lists', function (Request $request) {
    $visited = array_filter(array_map('intval', explode(',', $request->query('visited', ''))));
    $wishlist = array_filter(array_map('intval', explode(',', $request->query('wishlist', ''))));

    return response()->json([
        'visited'  => $visited  ? Volcano::whereIn('id', $visited)->get()  : [],
        'wishlist' => $wishlist ? Volcano::whereIn('id', $wishlist)->get() : [],
    ]);
});   

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
})->name('debug.image-paths');
