<?php

namespace App\Http\Controllers\Admin;

use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AchievementsAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Achievement::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $achievements = $query->paginate(20);
        return view('admin.manage-achievements', compact('achievements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:achievements',
            'description' => 'required|string',
            'metric' => 'required|string',
            'dimensions' => 'nullable|json',
            'aggregator' => 'required|string',
            'threshold' => 'required|integer|min:1',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'locked_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $directory = public_path('images/badges');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Handle unlocked image
        if ($request->hasFile('image_path')) {
            $imageName = $request->file('image_path')->getClientOriginalName();
            $request->file('image_path')->move($directory, $imageName);
            $validated['image_path'] = $imageName;
        } else {
            $validated['image_path'] = 'placeholder_unlocked.png';
        }

        // Handle locked image
        if ($request->hasFile('locked_image_path')) {
            $lockedImageName = $request->file('locked_image_path')->getClientOriginalName();
            $request->file('locked_image_path')->move($directory, $lockedImageName);
            $validated['locked_image_path'] = $lockedImageName;
        } else {
            $validated['locked_image_path'] = 'placeholder_locked.png';
        }

        Achievement::create($validated);
        
        return redirect()->route('admin.manage-achievements')->with('success', 'Achievement added successfully!');
    }

    public function destroy($id)
    {
        Achievement::find($id)->delete();
        return redirect()->route('admin.manage-achievements')->with('success', 'Achievement deleted successfully!');
    }
}