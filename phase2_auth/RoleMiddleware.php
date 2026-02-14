<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware - Role-Based Access Control
 * 
 * Checks if authenticated user has required role to access a route
 * Replaces has_access() function from includes/bootstrap.php
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles (Admin, Teacher, Staff, Student)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        // Get user's role
        $userRole = Auth::user()->role;

        // Admin has access to everything
        if ($userRole === 'Admin') {
            return $next($request);
        }

        // Check if user's role is in allowed roles
        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        // User doesn't have required role
        abort(403, 'You do not have permission to access this page.');
    }
}
