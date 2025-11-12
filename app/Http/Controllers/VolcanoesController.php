<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Volcano;
use App\Support\CountryAcronymMapper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class VolcanoesController extends Controller
{
    /**
     * Words that should be ignored when generating fallback search terms.
     *
     * @var array<int, string>
     */
    private array $searchStopWords = [
        'mount',
        'mt',
        'mt.',
        'mount.',
        'mountain',
        'volcano',
        'volcanoes',
        'volcan',
        'peak',
        'pico',
        'cerro',
        'the',
        'la',
        'el',
        'st',
        'st.',
        'saint',
        'de',
        'del'
    ];

    /**
     * Display the my volcanoes page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
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

        // If it's an AJAX request asking for stats, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'visited' => $visited,
                'wishlist' => $wishlist,
                'stats' => $stats
            ]);
        }

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
        $query = trim($request->get('query', ''));

        if ($query === '') {
            return response()->json([
                'success' => false,
                'message' => 'Query parameter is required'
            ]);
        }

        $normalizedQuery = $this->normalizeQuery($query);
        $significantTerms = $this->extractSignificantTerms($normalizedQuery);

        $volcanoes = $this->searchVolcanoesByTokens([$normalizedQuery]);

        if (!empty($significantTerms)) {
            $volcanoes = $volcanoes->merge(
                $this->searchVolcanoesByTokens($significantTerms)
            );
        }

        $countryMatches = $this->resolveCountryMatches($normalizedQuery, $significantTerms);

        if (!empty($countryMatches)) {
            $volcanoes = $volcanoes->merge(
                $this->searchVolcanoesByCountryNames($countryMatches)
            );
        }

        $volcanoes = $volcanoes->unique('id')->values();

        return response()->json([
            'success' => true,
            'data' => $volcanoes
        ]);
    }

    private function normalizeQuery(string $query): string
    {
        $query = preg_replace('/\s+/', ' ', $query);

        return trim($query);
    }

    private function extractSignificantTerms(string $query): array
    {
        $query = strtolower($query);
        $tokens = preg_split('/[\s,;:+\-]+/', $query) ?: [];

        $cleanTokens = [];

        foreach ($tokens as $token) {
            $token = trim($token, " \t\n\r\0\x0B'\".-_()/\\");

            if ($token === '') {
                continue;
            }

            if (in_array($token, $this->searchStopWords, true)) {
                continue;
            }

            $cleanTokens[] = $token;
        }

        return array_values(array_unique($cleanTokens));
    }

    private function searchVolcanoesByTokens(array $tokens): Collection
    {
        $tokens = array_values(array_filter(array_map('trim', $tokens)));

        if (empty($tokens)) {
            return collect();
        }

        return Volcano::where(function ($query) use ($tokens) {
            foreach ($tokens as $token) {
                $like = '%' . strtolower($token) . '%';

                $query->orWhere(function ($subQuery) use ($like) {
                    $subQuery->whereRaw('LOWER(name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(country) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(continent) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(type) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(activity) LIKE ?', [$like]);
                });
            }
        })->get();
    }

    private function resolveCountryMatches(string $normalizedQuery, array $tokens): array
    {
        $countryMapper = new CountryAcronymMapper();
        $matches = collect();

        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                $matches = $matches->merge($countryMapper->getCountryMatches($token));
            }
        }

        if ($matches->isEmpty()) {
            $matches = collect($countryMapper->getCountryMatches($normalizedQuery));
        }

        return $matches
            ->filter()
            ->map(fn ($country) => trim($country))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function searchVolcanoesByCountryNames(array $countries): Collection
    {
        if (empty($countries)) {
            return collect();
        }

        $countries = array_map(fn ($country) => strtolower($country), $countries);

        return Volcano::where(function ($query) use ($countries) {
            foreach ($countries as $country) {
                $likeCountry = '%' . $country . '%';
                $query->orWhereRaw('LOWER(country) LIKE ?', [$likeCountry]);
            }
        })->get();
    }
    
    public function getVolcano($id)
    {
        try {
            $volcano = Volcano::findOrFail($id);
            
            // Add the safe_image_url to the response
            $volcanoData = $volcano->toArray();
            $volcanoData['safe_image_url'] = $volcano->safe_image_url;
            
            return response()->json([
                'success' => true,
                'volcano' => $volcanoData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Volcano not found'
            ], 404);
        }
    }
}