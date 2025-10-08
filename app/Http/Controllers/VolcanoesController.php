<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}