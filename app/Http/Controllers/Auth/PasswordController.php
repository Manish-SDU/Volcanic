<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function showChange()
    {
        return view('auth.password-change');
    }

    public function change(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('status', 'Password updated successfully.');
    }

    public function showReset()
    {
        return view('auth.password-reset');
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !$user->date_of_birth || $user->date_of_birth->format('Y-m-d') !== $validated['date_of_birth']) {
            return back()
                ->withErrors(['username' => 'We could not verify those details.'])
                ->withInput($request->only(['username', 'date_of_birth']));
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('login')
            ->with('status', 'Password reset successfully. You can log in now.');
    }
}
