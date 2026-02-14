<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

/**
 * AuthController - Laravel Authentication
 * 
 * Replaces procedural authentication from index.php
 * Implements Laravel's built-in authentication system
 */
class AuthController extends Controller
{
    /**
     * Show the login form.
     * Converts: index.php (login view)
     */
    public function showLoginForm()
    {
        // Redirect to dashboard if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     * Converts: index.php (login processing)
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Prepare credentials for authentication
        // Note: We use 'user_id' field instead of default 'email'
        $loginCredentials = [
            'user_id' => $credentials['username'],
            'password' => $credentials['password'],
        ];

        // Remember me functionality
        $remember = $request->boolean('remember');

        // Attempt to authenticate
        if (Auth::attempt($loginCredentials, $remember)) {
            // Authentication successful
            $request->session()->regenerate(); // Prevent session fixation

            // Log authentication for audit
            \Log::info('User logged in', [
                'user_id' => Auth::user()->user_id,
                'role' => Auth::user()->role,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'username' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Handle logout request.
     * Converts: logout.php
     */
    public function logout(Request $request)
    {
        // Log logout for audit
        if (Auth::check()) {
            \Log::info('User logged out', [
                'user_id' => Auth::user()->user_id,
                'role' => Auth::user()->role,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the dashboard after login.
     * Converts: dashboard.php
     */
    public function dashboard()
    {
        // Get user's role
        $role = Auth::user()->role;

        // Get statistics based on role
        $stats = $this->getDashboardStats($role);

        return view('dashboard', compact('stats', 'role'));
    }

    /**
     * Get dashboard statistics based on user role.
     * 
     * @param string $role
     * @return array
     */
    private function getDashboardStats(string $role): array
    {
        $stats = [];

        // Admin gets full statistics
        if ($role === 'Admin') {
            $stats['total_students'] = \App\Models\Admission::count();
            $stats['total_staff'] = \App\Models\StaffDetail::count() ?? 0;
            $stats['pending_fees'] = \App\Models\StudentFee::where('status', 'pending')->sum('amount') ?? 0;
            $stats['total_books'] = \App\Models\Library\Book::count() ?? 0;
        }
        // Teacher gets limited statistics
        elseif ($role === 'Teacher') {
            $stats['total_students'] = \App\Models\Admission::count();
            $stats['my_classes'] = 0; // To be implemented
        }
        // Student sees their own information
        elseif ($role === 'Student') {
            $stats['my_fees'] = 0; // To be implemented
            $stats['my_books'] = 0; // To be implemented
        }

        return $stats;
    }
}
