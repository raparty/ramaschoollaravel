<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * SettingsController
 * 
 * Main controller for the Settings module dashboard
 */
class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        // Check permission
        $this->authorize('manage-settings');

        // Get statistics
        $stats = [
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
            'users_count' => User::count(),
            'active_roles' => Role::active()->count(),
        ];

        return view('settings.index', compact('stats'));
    }
}
