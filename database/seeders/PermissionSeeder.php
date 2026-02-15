<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Students Module
            ['module' => 'students', 'action' => 'view', 'description' => 'View student records'],
            ['module' => 'students', 'action' => 'create', 'description' => 'Add new students'],
            ['module' => 'students', 'action' => 'edit', 'description' => 'Edit student information'],
            ['module' => 'students', 'action' => 'delete', 'description' => 'Delete student records'],

            // Fees Module
            ['module' => 'fees', 'action' => 'view', 'description' => 'View fee records'],
            ['module' => 'fees', 'action' => 'create', 'description' => 'Collect fees'],
            ['module' => 'fees', 'action' => 'edit', 'description' => 'Edit fee records'],
            ['module' => 'fees', 'action' => 'delete', 'description' => 'Delete fee records'],

            // Library Module
            ['module' => 'library', 'action' => 'view', 'description' => 'View library records'],
            ['module' => 'library', 'action' => 'create', 'description' => 'Add new books'],
            ['module' => 'library', 'action' => 'edit', 'description' => 'Edit book information'],
            ['module' => 'library', 'action' => 'issue', 'description' => 'Issue books to students'],
            ['module' => 'library', 'action' => 'return', 'description' => 'Return books from students'],

            // Staff Module
            ['module' => 'staff', 'action' => 'view', 'description' => 'View staff records'],
            ['module' => 'staff', 'action' => 'create', 'description' => 'Add new staff'],
            ['module' => 'staff', 'action' => 'edit', 'description' => 'Edit staff information'],
            ['module' => 'staff', 'action' => 'delete', 'description' => 'Delete staff records'],

            // Attendance Module
            ['module' => 'attendance', 'action' => 'view', 'description' => 'View attendance records'],
            ['module' => 'attendance', 'action' => 'create', 'description' => 'Mark attendance'],
            ['module' => 'attendance', 'action' => 'edit', 'description' => 'Edit attendance records'],

            // Exams Module
            ['module' => 'exams', 'action' => 'view', 'description' => 'View exam schedules'],
            ['module' => 'exams', 'action' => 'create', 'description' => 'Create exam schedules'],
            ['module' => 'exams', 'action' => 'edit', 'description' => 'Edit exam schedules'],
            ['module' => 'exams', 'action' => 'marks', 'description' => 'Enter exam marks'],

            // Accounts Module
            ['module' => 'accounts', 'action' => 'view', 'description' => 'View financial records'],
            ['module' => 'accounts', 'action' => 'create', 'description' => 'Add income/expense'],
            ['module' => 'accounts', 'action' => 'edit', 'description' => 'Edit financial records'],

            // Reports Module
            ['module' => 'reports', 'action' => 'view', 'description' => 'View all reports'],

            // Transport Module
            ['module' => 'transport', 'action' => 'view', 'description' => 'View transport records'],
            ['module' => 'transport', 'action' => 'create', 'description' => 'Manage transport'],
            ['module' => 'transport', 'action' => 'edit', 'description' => 'Edit transport records'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                [
                    'module' => $permission['module'],
                    'action' => $permission['action'],
                ],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
