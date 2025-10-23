<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Volcano;
use App\Support\CountryAcronymMapper;
use Illuminate\Support\Facades\Log;

class VolcanoesController extends Controller
{
    /**
     * Display the my volcanoes page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('my-volcanoes.index');
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
        
        $countryMapper = new CountryAcronymMapper();
        $countryMatches = $countryMapper->getCountryMatches($query);
        
        // First, try direct country search (case insensitive)
        $countrySearch = Volcano::whereRaw('LOWER(country) LIKE LOWER(?)', ['%' . $query . '%'])->get();
        
        if ($countrySearch->count() > 0) {
            // Found volcanoes by country - this is a country search
            $volcanoes = $countrySearch;
        } elseif (!empty($countryMatches)) {
            // Use mapper matches if direct search didn't work
            $country = $countryMatches[0];
            $volcanoes = Volcano::whereRaw('LOWER(country) LIKE LOWER(?)', ['%' . $country . '%'])->get();
        } else {
            // Name search
            $volcanoes = Volcano::whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $query . '%'])->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $volcanoes
        ]);
    }
}