<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

/**
 * AuthServiceProvider - Authorization Gates and Policies
 * 
 * Defines authorization gates for the School ERP system
 * Maps to existing role_permissions table
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Admission' => 'App\Policies\AdmissionPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define authorization gates for each module and action
        $this->defineModuleGates();
    }

    /**
     * Define authorization gates for all modules.
     */
    private function defineModuleGates(): void
    {
        // Students Module Gates
        Gate::define('view-students', function (User $user) {
            return $user->hasPermission('students', 'view');
        });

        Gate::define('create-students', function (User $user) {
            return $user->hasPermission('students', 'create');
        });

        Gate::define('edit-students', function (User $user) {
            return $user->hasPermission('students', 'edit');
        });

        Gate::define('delete-students', function (User $user) {
            return $user->hasPermission('students', 'delete');
        });

        // Fees Module Gates
        Gate::define('view-fees', function (User $user) {
            return $user->hasPermission('fees', 'view');
        });

        Gate::define('create-fees', function (User $user) {
            return $user->hasPermission('fees', 'create');
        });

        Gate::define('edit-fees', function (User $user) {
            return $user->hasPermission('fees', 'edit');
        });

        Gate::define('delete-fees', function (User $user) {
            return $user->hasPermission('fees', 'delete');
        });

        // Library Module Gates
        Gate::define('view-library', function (User $user) {
            return $user->hasPermission('library', 'view');
        });

        Gate::define('manage-library', function (User $user) {
            return $user->hasPermission('library', 'create') 
                || $user->hasPermission('library', 'edit');
        });

        Gate::define('issue-books', function (User $user) {
            return $user->hasPermission('library', 'issue');
        });

        Gate::define('return-books', function (User $user) {
            return $user->hasPermission('library', 'return');
        });

        // Staff Module Gates
        Gate::define('view-staff', function (User $user) {
            return $user->hasPermission('staff', 'view');
        });

        Gate::define('manage-staff', function (User $user) {
            return $user->hasPermission('staff', 'create') 
                || $user->hasPermission('staff', 'edit');
        });

        // Attendance Module Gates
        Gate::define('view-attendance', function (User $user) {
            return $user->hasPermission('attendance', 'view');
        });

        Gate::define('mark-attendance', function (User $user) {
            return $user->hasPermission('attendance', 'create');
        });

        // Exams Module Gates
        Gate::define('view-exams', function (User $user) {
            return $user->hasPermission('exams', 'view');
        });

        Gate::define('manage-exams', function (User $user) {
            return $user->hasPermission('exams', 'create') 
                || $user->hasPermission('exams', 'edit');
        });

        Gate::define('enter-marks', function (User $user) {
            return $user->hasPermission('exams', 'marks');
        });

        // Transport Module Gates
        Gate::define('view-transport', function (User $user) {
            return $user->hasPermission('transport', 'view');
        });

        Gate::define('manage-transport', function (User $user) {
            return $user->hasPermission('transport', 'create') 
                || $user->hasPermission('transport', 'edit');
        });

        // Accounts Module Gates
        Gate::define('view-accounts', function (User $user) {
            return $user->hasPermission('accounts', 'view');
        });

        Gate::define('manage-accounts', function (User $user) {
            return $user->hasPermission('accounts', 'create') 
                || $user->hasPermission('accounts', 'edit');
        });

        // Reports Module Gates
        Gate::define('view-reports', function (User $user) {
            return $user->hasPermission('reports', 'view');
        });

        // Settings Module Gates (Admin only)
        Gate::define('manage-settings', function (User $user) {
            return $user->isAdmin();
        });

        // Super gate for admin - before other gates are checked
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true; // Admin has all permissions
            }
        });
    }
}
