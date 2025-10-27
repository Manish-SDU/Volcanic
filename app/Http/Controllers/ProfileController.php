<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // current authenticated user
        return view('profile.index', compact('user')); // or 'profile' depending on your file path
    }
}