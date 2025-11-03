<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Achievement;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all achievements
        $allAchievements = Achievement::all();
        
        // Get user's unlocked achievements
        $unlockedAchievements = $user->achievements()->get();
        
        // Get locked achievements (ones the user hasn't earned yet)
        $lockedAchievements = $allAchievements->diff($unlockedAchievements);
        
        // Get visit count for progress bar
        $visitCount = $user->userVolcanoes()->count();
        $nextMilestone = 5; // Next milestone is 5 visits
        if ($visitCount >= 5) $nextMilestone = 10;
        if ($visitCount >= 10) $nextMilestone = 25;
        if ($visitCount >= 25) $nextMilestone = 50;
        
        return view('profile.index', compact('user', 'unlockedAchievements', 'lockedAchievements', 'visitCount', 'nextMilestone'));
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