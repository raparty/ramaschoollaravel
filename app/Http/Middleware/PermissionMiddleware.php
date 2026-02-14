<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * PermissionMiddleware - Permission-Based Access Control
 * 
 * Checks if authenticated user has specific permission for a module and action
 * Replaces has_access() function from includes/bootstrap.php
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module   Module name (e.g., 'students', 'fees', 'library')
     * @param  string  $action   Action name (e.g., 'view', 'create', 'edit', 'delete')
     */
    public function handle(Request $request, Closure $next, string $module, string $action): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Admin has all permissions
        if ($user->role === 'Admin') {
            return $next($request);
        }

        // Check if user has specific permission
        if ($user->hasPermission($module, $action)) {
            return $next($request);
        }

        // User doesn't have required permission
        abort(403, 'You do not have permission to perform this action.');
    }
}
