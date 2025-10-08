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
    public function index()
    {
        // Cache all volcanoes for 24 hours to improve performance - will limit display via CSS/JS
        $volcanoes = Cache::remember('home_volcanoes', 60*60*24, function () {
            return Volcano::select('id', 'name', 'country', 'type', 'activity', 'elevation', 'image_url', 'latitude', 'longitude', 'description')
                ->orderBy('name')
                ->get();
        });
        
        return view('home.index', compact('volcanoes'));
    }
}