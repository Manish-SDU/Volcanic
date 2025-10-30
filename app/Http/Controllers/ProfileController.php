<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the profile.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // Only allow safe-to-edit fields (no password here, no is_admin)
        $data = $request->only([
            'name',
            'surname',
            'username',
            'date_of_birth',
            'where_from',
            'bio',
        ]);

        $user->fill($data);
        $user->save();

        return redirect()
            ->route('profile')
            ->with('status', 'Profile updated successfully.');
    }
}