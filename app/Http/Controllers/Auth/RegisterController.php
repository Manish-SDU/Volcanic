<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'surname'       => ['nullable', 'string', 'max:100'],
            'username'      => ['required', 'string', 'max:60', 'alpha_dash', 'unique:users,username'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'where_from'    => ['nullable', 'string', 'max:100'],
            'bio'           => ['nullable', 'string', 'max:5000'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'surname'       => $validated['surname'] ?? null,
            'username'      => $validated['username'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'where_from'    => $validated['where_from'] ?? null,
            'bio'           => $validated['bio'] ?? null,
            'password'      => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('my-volcanoes');
    }
}
