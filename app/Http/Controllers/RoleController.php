<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * RoleController
 * 
 * Handles CRUD operations for roles
 */
class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $this->authorize('manage-settings');

        $roles = Role::withCount(['permissions'])->orderBy('role_name')->paginate(15);
        
        // Get user counts for each role
        foreach ($roles as $role) {
            $role->users_count = DB::table('users')->where('role', $role->role_name)->count();
        }

        return view('settings.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorize('manage-settings');

        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');

        return view('settings.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-settings');

        $validated = $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,role_name',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'role_name' => $validated['role_name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // Assign permissions
            if (!empty($validated['permissions'])) {
                foreach ($validated['permissions'] as $permissionId) {
                    RolePermission::create([
                        'role' => $role->role_name,
                        'permission_id' => $permissionId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('settings.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorize('manage-settings');

        $role->load('permissions');
        $usersCount = DB::table('users')->where('role', $role->role_name)->count();

        return view('settings.roles.show', compact('role', 'usersCount'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorize('manage-settings');

        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('settings.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('manage-settings');

        $validated = $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,role_name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $oldRoleName = $role->role_name;

            $role->update([
                'role_name' => $validated['role_name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // If role name changed, update role_permissions and users tables
            if ($oldRoleName !== $validated['role_name']) {
                RolePermission::where('role', $oldRoleName)->update(['role' => $validated['role_name']]);
                DB::table('users')->where('role', $oldRoleName)->update(['role' => $validated['role_name']]);
            }

            // Update permissions
            RolePermission::where('role', $role->role_name)->delete();
            if (!empty($validated['permissions'])) {
                foreach ($validated['permissions'] as $permissionId) {
                    RolePermission::create([
                        'role' => $role->role_name,
                        'permission_id' => $permissionId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('settings.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorize('manage-settings');

        try {
            // Check if role has users
            $usersCount = DB::table('users')->where('role', $role->role_name)->count();
            if ($usersCount > 0) {
                return back()->with('error', "Cannot delete role. $usersCount user(s) are assigned to this role.");
            }

            // Delete role permissions
            RolePermission::where('role', $role->role_name)->delete();

            // Delete role
            $role->delete();

            return redirect()->route('settings.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}
