<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $users = $query->paginate(20);
        return view('admin.manage-users', compact('users'));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.manage-users')->with('success', 'User deleted successfully!');
    }
}