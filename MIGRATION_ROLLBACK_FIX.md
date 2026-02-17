# Migration Rollback Issue - Fix Guide

## Problem
When attempting to rollback the hostel management migration using:
```bash
php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
```

The error occurs:
```
2026_02_17_053600_add_emergency_contact_and_health_fields_to_admissions_table .... Migration not found
```

## Root Cause
Both migrations were run in the same batch (batch number in the migrations table). When using `--path` with a specific file, Laravel tries to rollback all migrations in that batch, but can only find migrations within the specified path. Since the emergency contact migration is in a different file, it reports "Migration not found".

## Solutions

### Solution 1: Rollback without --path (Recommended)
Simply rollback the last batch without specifying a path:
```bash
php artisan migrate:rollback
```
This will rollback both migrations that were run in the same batch.

### Solution 2: Rollback with directory path
Instead of specifying a specific file, specify the directory:
```bash
php artisan migrate:rollback --path=database/migrations
```

### Solution 3: Rollback by steps
Rollback a specific number of migrations:
```bash
php artisan migrate:rollback --step=1
```
This will rollback only the last migration batch.

### Solution 4: Manual rollback of specific migration
If you only want to rollback the hostel management tables:

1. First, manually update the migrations table to remove the hostel migration:
```sql
DELETE FROM migrations WHERE migration = '2026_02_17_083400_create_hostel_management_tables';
```

2. Then manually run the down migration for hostel tables:
```bash
php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
```

However, this approach is not recommended as it breaks batch integrity.

## Best Practice
To avoid this issue in the future:
- Run migrations that are logically separate in different batches
- Or accept that migrations run together should be rolled back together
- Always use `php artisan migrate:rollback` without --path unless you have a specific reason

## For This Specific Case
Since both migrations modify different aspects of the system:
- Emergency contact migration: Adds fields to admissions table
- Hostel migration: Creates hostel management tables

They can be safely rolled back together. Use:
```bash
php artisan migrate:rollback
```

If you only want to rollback the hostel migration, you'll need to manually handle the database as described in Solution 4.
