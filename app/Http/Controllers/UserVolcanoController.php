<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserVolcano;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserVolcanoController extends Controller
{
    // Toggle volcano status (visited/wishlist)

    public function toggleStatus(Request $request, $volcanoId, $status)
    {
        if (!in_array($status, ['visited', 'wishlist'])) {
            return back()->with('error', 'Invalid status');
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
                    return back()->with('success', ucfirst($status) . ' removed!');
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
            UserVolcano::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'volcanoes_id' => $volcanoId,
                ],
                ['status' => $status]
            );

            return back()->with('success', 'Added to ' . $status . '!');

        } catch (\Exception $e) {
            Log::error('Error toggling volcano status: ' . $e->getMessage());
            return back()->with('success', 'Added to ' . $status . '!');
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
            $status = UserVolcano::where([
                'user_id' => Auth::id(),
                'volcanoes_id' => $volcanoId
            ])->value('status');

            return response()->json([
                'status' => $status,
                'isVisited' => $status === 'visited',
                'isWishlisted' => $status === 'wishlist'
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking volcano status: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}