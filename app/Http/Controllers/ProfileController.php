<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('profile.index');
    }
}