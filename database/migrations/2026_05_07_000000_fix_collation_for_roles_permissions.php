<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fixes collation mismatch errors when joining roles and permissions tables.
     * Error: SQLSTATE[HY000]: General error: 1267 Illegal mix of collations
     * 
     * This migration converts all related tables to use utf8mb4_unicode_ci collation
     * to ensure consistency across the database.
     */
    public function up(): void
    {
        // Get the current database name
        $database = DB::selectOne('SELECT DATABASE() as db')->db;

        // Fix collation for roles table
        if (Schema::hasTable('roles')) {
            DB::statement(
                "ALTER TABLE `roles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }

        // Fix collation for permissions table
        if (Schema::hasTable('permissions')) {
            DB::statement(
                "ALTER TABLE `permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }

        // Fix collation for role_permissions table
        if (Schema::hasTable('role_permissions')) {
            DB::statement(
                "ALTER TABLE `role_permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }

        // Also standardize other core tables for consistency
        $tables = [
            'users',
            'admissions',
            'classes',
            'sections',
            'streams',
            'subjects',
            'terms',
            'schools',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement(
                    "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Reverts tables back to utf8mb4_0900_ai_ci (MySQL 8.0+ default)
     * Note: This may reintroduce the collation mismatch issue
     */
    public function down(): void
    {
        // Get the current database name
        $database = DB::selectOne('SELECT DATABASE() as db')->db;

        // Revert collation back to utf8mb4_0900_ai_ci
        $tables = [
            'roles',
            'permissions',
            'role_permissions',
            'users',
            'admissions',
            'classes',
            'sections',
            'streams',
            'subjects',
            'terms',
            'schools',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement(
                    "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci"
                );
            }
        }
    }
};
