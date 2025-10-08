<?php

use App\Models\Volcano;
use Illuminate\Support\Facades\Route;

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