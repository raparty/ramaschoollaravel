<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * UserManagementController
 * 
 * Handles user role assignments
 */
class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('manage-settings');

        $query = DB::table('users')->orderBy('full_name');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_id', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%");
            });
        }

        $users = $query->paginate(20);
        $roles = Role::active()->orderBy('role_name')->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for editing user role.
     */
    public function edit($id)
    {
        $this->authorize('manage-settings');

        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect()->route('settings.users.index')
                ->with('error', 'User not found.');
        }

        $roles = Role::active()->orderBy('role_name')->get();

        return view('settings.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user role.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('manage-settings');

        $validated = $request->validate([
            'role' => 'required|exists:roles,role_name',
        ]);

        try {
            $user = DB::table('users')->where('id', $id)->first();
            
            if (!$user) {
                return redirect()->route('settings.users.index')
                    ->with('error', 'User not found.');
            }

            DB::table('users')
                ->where('id', $id)
                ->update(['role' => $validated['role']]);

            return redirect()->route('settings.users.index')
                ->with('success', 'User role updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user role: ' . $e->getMessage());
        }
    }
}
