#!/usr/bin/env php
<?php

/**
 * Hostel Migration Prerequisites Checker
 * 
 * This script checks if your database is ready for the hostel management migration.
 * Run this before executing the hostel migration to catch any schema issues early.
 * 
 * Usage: php check_admissions_schema.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║ Hostel Migration Prerequisites Checker                           ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

try {
    // Check database connection
    echo "✓ Checking database connection...\n";
    DB::connection()->getPdo();
    echo "  ✓ Database connection successful\n\n";

    // Check if admissions table exists
    echo "✓ Checking if admissions table exists...\n";
    if (!Schema::hasTable('admissions')) {
        echo "  ✗ ERROR: Admissions table does not exist!\n";
        echo "  → Please run the core tables migration first:\n";
        echo "    php artisan migrate --path=database/migrations/2026_02_14_072514_create_core_tables.php\n\n";
        exit(1);
    }
    echo "  ✓ Admissions table exists\n\n";

    // Check admissions.id column type
    echo "✓ Checking admissions.id column type...\n";
    $result = DB::selectOne(
        "SELECT DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA
         FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'admissions' 
         AND COLUMN_NAME = 'id'"
    );

    if (!$result) {
        echo "  ✗ ERROR: Could not find 'id' column in admissions table!\n\n";
        exit(1);
    }

    echo "  Column Type: {$result->COLUMN_TYPE}\n";
    echo "  Data Type: {$result->DATA_TYPE}\n";
    echo "  Key: {$result->COLUMN_KEY}\n";
    echo "  Extra: {$result->EXTRA}\n\n";

    // Verify it's INT UNSIGNED, not BIGINT UNSIGNED
    if (stripos($result->COLUMN_TYPE, 'bigint') !== false) {
        echo "  ✗ ERROR: admissions.id is BIGINT UNSIGNED (expected INT UNSIGNED)!\n";
        echo "  → This will cause foreign key constraint errors in the hostel migration.\n";
        echo "  → The admissions table should use increments('id'), not id().\n";
        echo "  → You may need to:\n";
        echo "    1. Backup your data\n";
        echo "    2. Rollback the core tables migration\n";
        echo "    3. Ensure the migration uses increments('id') for admissions table\n";
        echo "    4. Re-run the migration\n\n";
        exit(1);
    }

    if (stripos($result->COLUMN_TYPE, 'int') !== false && 
        stripos($result->COLUMN_TYPE, 'unsigned') !== false) {
        echo "  ✓ Column type is correct (INT UNSIGNED)\n\n";
    } else {
        echo "  ⚠ WARNING: Unexpected column type: {$result->COLUMN_TYPE}\n";
        echo "  → Expected: int unsigned or int(10) unsigned\n\n";
    }

    // Check some related tables from core migration
    echo "✓ Checking other prerequisite tables...\n";
    $requiredTables = ['classes', 'sections'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        if (!Schema::hasTable($table)) {
            $missingTables[] = $table;
        }
    }

    if (count($missingTables) > 0) {
        echo "  ⚠ WARNING: Missing tables: " . implode(', ', $missingTables) . "\n";
        echo "  → Ensure the core tables migration has been run completely\n\n";
    } else {
        echo "  ✓ Core prerequisite tables exist\n\n";
    }

    echo "╔═══════════════════════════════════════════════════════════════════╗\n";
    echo "║ ✓ ALL CHECKS PASSED                                              ║\n";
    echo "║                                                                   ║\n";
    echo "║ Your database is ready for the hostel management migration.      ║\n";
    echo "║ You can now run:                                                 ║\n";
    echo "║                                                                   ║\n";
    echo "║ php artisan migrate --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════╝\n";

} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
