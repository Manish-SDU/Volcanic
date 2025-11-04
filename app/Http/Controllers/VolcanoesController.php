<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Volcano;
use App\Support\CountryAcronymMapper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VolcanoesController extends Controller
{
    /**
     * Display the my volcanoes page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
    
        // Get user's volcanoes grouped by status
        $userVolcanoes = \App\Models\UserVolcano::where('user_id', $user->id)
            ->with('volcano')
            ->get()
            ->groupBy('status');
        
        $visited = $userVolcanoes->get('visited', collect());
        $wishlist = $userVolcanoes->get('wishlist', collect());

        $stats = $this->calculateStats($visited);

        return view('my-volcanoes.index', [
            'visited' => $visited,
            'wishlist' => $wishlist,
            'stats' => $stats,
        ]);
    }

    /**
     * Calculate statistics 
     */

    private function calculateStats($visitedVolcanoes)
    {
        $totalVolcanoes = $visitedVolcanoes->count();

        $countriesExplored = $visitedVolcanoes
            ->pluck('volcano.country')
            ->unique()
            ->count();

        $activeVolcanoes = $visitedVolcanoes
            ->filter(function($userV) {
                return strtolower($userV->volcano->activity) ==='active';
            })
            ->count();

        $inactiveVolcanoes = $visitedVolcanoes
            ->filter(function($userV) {
                $activity = strtolower($userV->volcano->activity);
                return in_array($activity, ['inactive', 'extinct']);
            })
            ->count();

        return[
            'volcanoes_visited' => $totalVolcanoes,
            'countries_explored' => $countriesExplored,
            'active_volcanoes' => $activeVolcanoes,
            'inactive_volcanoes' => $inactiveVolcanoes,
        ];
    }

    /**
     * Search for volcanoes.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query parameter is required'
            ]);
        }
        
        // Search across multiple fields simultaneously
        // This ensures we catch all matches regardless of field
        $volcanoes = Volcano::where(function($q) use ($query) {
            // Search in name field
            $q->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $query . '%'])
              // Search in country field
              ->orWhereRaw('LOWER(country) LIKE LOWER(?)', ['%' . $query . '%'])
              // Search in continent field
              ->orWhereRaw('LOWER(continent) LIKE LOWER(?)', ['%' . $query . '%'])
              // Search in type field
              ->orWhereRaw('LOWER(type) LIKE LOWER(?)', ['%' . $query . '%'])
              // Search in activity field
              ->orWhereRaw('LOWER(activity) LIKE LOWER(?)', ['%' . $query . '%']);
        })->get();
        
        // If no results found, try with country acronym mapper as fallback
        if ($volcanoes->isEmpty()) {
            $countryMapper = new CountryAcronymMapper();
            $countryMatches = $countryMapper->getCountryMatches($query);
            
            if (!empty($countryMatches)) {
                $country = $countryMatches[0];
                $volcanoes = Volcano::whereRaw('LOWER(country) LIKE LOWER(?)', ['%' . $country . '%'])->get();
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $volcanoes
        ]);
    }
}