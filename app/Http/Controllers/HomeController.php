<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Volcano;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Volcano::query();
        $demoLimit = (int) env('DEMO_VOLCANO_LIMIT', 0);
        
        // Apply filters if present
        $hasFilters = false;
        
        if ($request->filled('country')) {
            $query->where('country', $request->country);
            $hasFilters = true;
        }
        
        if ($request->filled('continent')) {
            $query->where('continent', $request->continent);
            $hasFilters = true;
        }
        
        if ($request->filled('activity')) {
            $query->where('activity', $request->activity);
            $hasFilters = true;
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
            $hasFilters = true;
        }
        
        if ($request->filled('elevation_min')) {
            $query->where('elevation', '>=', $request->elevation_min);
            $hasFilters = true;
        }
        
        if ($request->filled('elevation_max')) {
            $query->where('elevation', '<=', $request->elevation_max);
            $hasFilters = true;
        }
        
        // Get sort parameter (only applied when no filters are active)
        $sortOption = $request->get('sort', 'alphabetical');
        
        // If filters are applied, don't use cache and ignore sort
        if ($hasFilters) {
            $filteredQuery = $query->select('id', 'name', 'country', 'type', 'activity', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
                ->orderBy('name');

            if ($demoLimit > 0) {
                $filteredQuery->limit($demoLimit);
            }

            $volcanoes = $filteredQuery->get();
        } else {
            // Apply sorting based on user preference (only when no filters)
            if ($sortOption === 'random') {
                // For random sorting, don't use cache and apply random order
                $randomQuery = Volcano::select('id', 'name', 'country', 'type', 'activity', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
                    ->inRandomOrder();

                if ($demoLimit > 0) {
                    $randomQuery->limit($demoLimit);
                }

                $volcanoes = $randomQuery->get();
            } else {
                // Cache alphabetically sorted volcanoes for 24 hours to improve performance
                $cacheKey = $demoLimit > 0 ? "home_volcanoes_limit_{$demoLimit}" : 'home_volcanoes';
                $volcanoes = Cache::remember($cacheKey, 60*60*24, function () use ($demoLimit) {
                    $baseQuery = Volcano::select('id', 'name', 'country', 'type', 'activity', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
                        ->orderBy('name');

                    if ($demoLimit > 0) {
                        $baseQuery->limit($demoLimit);
                    }

                    return $baseQuery->get();
                });
            }
        }
        
        return view('home.index', compact('volcanoes'));
    }
}