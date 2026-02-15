<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * PermissionController
 * 
 * Handles permissions management
 */
class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $this->authorize('manage-settings');

        $permissions = Permission::orderBy('module')->orderBy('action')->get();
        $grouped = $permissions->groupBy('module');
        $modules = Permission::getModules();

        return view('settings.permissions.index', compact('grouped', 'modules'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $this->authorize('manage-settings');

        $modules = Permission::getModules();

        return view('settings.permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-settings');

        $validated = $request->validate([
            'module' => 'required|string|max:50',
            'submodule' => 'nullable|string|max:100',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            // Check for duplicate (considering submodule)
            $query = Permission::where('module', $validated['module'])
                ->where('action', $validated['action']);
            
            if (isset($validated['submodule'])) {
                $query->where('submodule', $validated['submodule']);
            } else {
                $query->whereNull('submodule');
            }
            
            if ($query->exists()) {
                return back()->withInput()
                    ->with('error', 'Permission already exists for this module, action, and submodule combination.');
            }

            Permission::create($validated);

            return redirect()->route('settings.permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('manage-settings');

        try {
            // Check if permission is used by any roles
            $rolesCount = $permission->roles()->count();
            if ($rolesCount > 0) {
                return back()->with('error', "Cannot delete permission. It is assigned to $rolesCount role(s).");
            }

            $permission->delete();

            return redirect()->route('settings.permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }
}
