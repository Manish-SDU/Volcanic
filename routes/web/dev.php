<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchemaController;
use App\Models\Volcano;

return function () {
    // Schema route (for development only)
    Route::get('/schema', [SchemaController::class, 'showSchema']);

    // Debug route for checking volcano image paths
    Route::get('/debug/image-paths', function () {
        $volcanoes = Volcano::all();
        $paths = [];

        foreach ($volcanoes as $volcano) {
            $baseName = strtolower(str_replace(' ', '_', $volcano->name));
            $paths[] = [
                'volcano_name'   => $volcano->name,
                'base_name'      => $baseName,
                'safe_image_url' => $volcano->safe_image_url,
                'file_exists'    => file_exists(public_path("images/volcanoes/{$baseName}.png")),
            ];
        }

        return response()->json($paths);
    })->name('debug.image-paths');
};
