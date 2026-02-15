<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            [
                'role_name' => 'Admin',
                'description' => 'Full system access with all permissions',
                'status' => 'active',
            ],
            [
                'role_name' => 'Principal',
                'description' => 'School principal with access to most features',
                'status' => 'active',
            ],
            [
                'role_name' => 'Teacher',
                'description' => 'Teaching staff with student and exam management access',
                'status' => 'active',
            ],
            [
                'role_name' => 'Accountant',
                'description' => 'Financial management and fee collection',
                'status' => 'active',
            ],
            [
                'role_name' => 'Librarian',
                'description' => 'Library management and book issue/return',
                'status' => 'active',
            ],
            [
                'role_name' => 'Receptionist',
                'description' => 'Front desk operations and student admission',
                'status' => 'active',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['role_name' => $roleData['role_name']],
                $roleData
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Roles and permissions assigned successfully!');
    }

    /**
     * Assign permissions to roles based on their function.
     */
    private function assignPermissionsToRoles()
    {
        // Admin gets all permissions (handled by Gate::before in AuthServiceProvider)
        
        // Principal - Almost all permissions except delete
        $principal = Role::where('role_name', 'Principal')->first();
        $principalPermissions = Permission::whereIn('action', ['view', 'create', 'edit', 'marks'])->pluck('id');
        $this->syncRolePermissions($principal, $principalPermissions);

        // Teacher - Students, attendance, exams
        $teacher = Role::where('role_name', 'Teacher')->first();
        $teacherPermissions = Permission::where(function($q) {
            $q->where('module', 'students')->whereIn('action', ['view', 'edit'])
              ->orWhere('module', 'attendance')
              ->orWhere('module', 'exams')
              ->orWhere('module', 'reports')->where('action', 'view');
        })->pluck('id');
        $this->syncRolePermissions($teacher, $teacherPermissions);

        // Accountant - Fees, accounts, reports
        $accountant = Role::where('role_name', 'Accountant')->first();
        $accountantPermissions = Permission::where(function($q) {
            $q->where('module', 'fees')
              ->orWhere('module', 'accounts')
              ->orWhere('module', 'reports')->where('action', 'view')
              ->orWhere('module', 'students')->where('action', 'view');
        })->pluck('id');
        $this->syncRolePermissions($accountant, $accountantPermissions);

        // Librarian - Library only
        $librarian = Role::where('role_name', 'Librarian')->first();
        $librarianPermissions = Permission::where(function($q) {
            $q->where('module', 'library')
              ->orWhere('module', 'students')->where('action', 'view');
        })->pluck('id');
        $this->syncRolePermissions($librarian, $librarianPermissions);

        // Receptionist - Students admission and basic viewing
        $receptionist = Role::where('role_name', 'Receptionist')->first();
        $receptionistPermissions = Permission::where(function($q) {
            $q->where('module', 'students')
              ->orWhere('module', 'reports')->where('action', 'view');
        })->pluck('id');
        $this->syncRolePermissions($receptionist, $receptionistPermissions);
    }

    /**
     * Sync permissions to a role.
     */
    private function syncRolePermissions($role, $permissionIds)
    {
        if (!$role) return;

        // Delete existing permissions for this role
        DB::table('role_permissions')->where('role', $role->role_name)->delete();

        // Insert new permissions
        foreach ($permissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role' => $role->role_name,
                'permission_id' => $permissionId,
                'created_at' => now(),
            ]);
        }
    }
}
