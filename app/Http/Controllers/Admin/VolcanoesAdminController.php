<?php

namespace App\Http\Controllers\Admin;

use App\Models\Volcano;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VolcanoesAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Volcano::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
        }
        
        $volcanoes = $query->paginate(40);
        return view('admin.manage-volcanoes', compact('volcanoes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:volcanoes',
            'country' => 'required|string',
            'continent' => 'required|string',
            'activity' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'elevation' => 'required|integer',
            'type' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'description' => 'required|string',
        ]);

        if ($request->hasFile('image_url')) {
            // Ensuring that the images/volcanoes directory exists
            $directory = public_path('images/volcanoes');
            // If the directory doesn't exist, create it.
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Store the image and get the filename
            $imageName = $request->file('image_url')->getClientOriginalName();
            $request->file('image_url')->move($directory, $imageName);
            $validated['image_url'] = $imageName;
        } else {
            // If no image is provided, placeholder will be set as the image
            $validated['image_url'] = 'placeholder.png';
        }

        Volcano::create($validated);
        
        
        return redirect()->route('admin.manage-volcanoes')->with('success', 'Volcano added successfully!');
    }

    public function destroy($id)
    {
        Volcano::find($id)->delete();
        return redirect()->route('admin.manage-volcanoes')->with('success', 'Volcano deleted successfully!');
    }
}