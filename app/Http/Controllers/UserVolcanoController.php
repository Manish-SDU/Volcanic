<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserVolcano;
use App\Models\Volcano;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\AchievementService;

class UserVolcanoController extends Controller
{
    // Toggle volcano status (visited/wishlist)

    public function toggleStatus(Request $request, $volcanoId, $status)
    {
        if (!in_array($status, ['visited', 'wishlist'])) {
            return response()->json(['success' => false, 'error' => 'Invalid status'], 400);
        }

        try {
            // Check if entry exists
            $existing = UserVolcano::where([
                'user_id' => Auth::id(),
                'volcanoes_id' => $volcanoId,
            ])->first();

            if ($existing) {
                // If same status, remove it (toggle off)
                if ($existing->status === $status) {
                    $existing->delete();
                    return response()->json([
                        'success' => true,
                        'message' => ucfirst($status) . ' removed!',
                        'action' => 'removed'
                    ]);
                }
            }

            // If marking as visited, remove from wishlist
            if ($status === 'visited') {
                UserVolcano::where([
                    'user_id' => Auth::id(),
                    'volcanoes_id' => $volcanoId,
                    'status' => 'wishlist'
                ])->delete();
            }

            // Create or update entry
            $data = ['status' => $status];
            
            // Set visited_at only when status is 'visited'
            if ($status === 'visited') {
                $data['visited_at'] = now();
            }

            UserVolcano::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'volcanoes_id' => $volcanoId,
                ],
                $data
            );

            return response()->json([
            'success' => true,
            'message' => 'Added to ' . $status . '!',
            'action' => 'added'
        ]);

        } catch (\Exception $e) {
            Log::error('Error toggling volcano status: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    //Get user's volcano lists
    public function getLists()
    {
        try {
            $lists = UserVolcano::where('user_id', Auth::id())
                ->with('volcano')
                ->get()
                ->groupBy('status');

            return response()->json([
                'visited' => $lists['visited'] ?? [],
                'wishlist' => $lists['wishlist'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching volcano lists: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // Check volcano status for current user
    public function checkStatus($volcanoId)
    {
        try {
            $userVolcano = UserVolcano::where([
                'user_id' => Auth::id(),
                'volcanoes_id' => $volcanoId
            ])->first();

            return response()->json([
                'status' => $userVolcano?->status,
                'isVisited' => $userVolcano?->status === 'visited',
                'isWishlisted' => $userVolcano?->status === 'wishlist',
                'visited_at' => $userVolcano?->visited_at
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking volcano status: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}