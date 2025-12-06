<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VolcanoRealtimeController; 

Route::post('/gemini', function (Request $request) {
    $prompt = $request->input('prompt');

    // Add context to make answers shorter and volcano-focused
    $systemPrompt = "You are a helpful volcano expert assistant. Give concise, informative answers in 2-3 sentences maximum. Focus on volcanoes, geology, and related topics.";
    
    $fullPrompt = $systemPrompt . "\n\nUser question: " . $prompt;

    $response = Http::withOptions([
        'verify' => false  // <-- Add this to disable SSL verification for development only
    ])->withHeaders([
        'Content-Type' => 'application/json'
    ])->post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'),
        [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ]
        ]
    );
    
    return $response->json();
});

Route::get('/volcano/latest', [VolcanoRealtimeController::class, 'latest'])
    ->name('api.volcano.latest');