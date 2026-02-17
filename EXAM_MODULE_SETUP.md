# Exams Module Setup Guide

## Problem
The Exams module link in the sidebar redirects back to the dashboard when clicked.

## Root Cause
The exams module requires three database tables that may not exist in your database:
- `terms` - Academic terms/semesters
- `exams` - Exam schedules
- `exam_subjects` - Subjects assigned to exams with max marks

## Solution

### Option 1: Run Laravel Migrations (Recommended)
```bash
php artisan migrate
```

This will create all missing tables including the exam-related tables.

### Option 2: Manual SQL Installation
If you prefer to manually create the tables, run the SQL file:

```bash
mysql -u your_username -p your_database < database/schema/exam_tables.sql
```

Or execute the SQL directly in your database management tool (phpMyAdmin, MySQL Workbench, etc.)

### Option 3: Run Migration Specifically
If you only want to run the core tables migration:

```bash
php artisan migrate --path=/database/migrations/2026_02_14_072514_create_core_tables.php
```

## Verification

After running the migration or SQL file, the exams module should work. You can verify by:

1. Click on the "📝 Exams" link in the sidebar
2. You should see the Exams page instead of being redirected to the dashboard
3. Initially, you'll see "No Exams Found" which is normal for a fresh installation

## Creating Your First Exam

1. Click "Create Exam" button
2. However, you'll first need to create at least one Academic Term:
   - Academic terms are required for exams
   - The system uses terms like "2023-2024", "Term 1 2024", etc.
   - **Note:** Currently there's no UI to create terms - this needs to be added
   - As a workaround, insert a term directly into the database:

```sql
INSERT INTO terms (name, start_date, end_date, is_active, created_at, updated_at) 
VALUES ('2024-2025', '2024-04-01', '2025-03-31', 1, NOW(), NOW());
```

## Legacy Database Compatibility

If you're migrating from the old PHP system, note that:
- Old system used `exam_nuber_of_term` table (with typo "nuber")
- New Laravel system uses `terms` table
- Both can coexist; the Laravel system uses its own tables

## Migration Safety

The migration file has been updated to include safety checks:
- Uses `Schema::hasTable()` to prevent "table already exists" errors
- Safe to run multiple times
- Won't overwrite existing data

## Troubleshooting

### Still getting redirected to dashboard?
Check the error message shown at the top of the dashboard page. It will say:
> "Database tables are not set up. Please run migrations first using: php artisan migrate"

### Foreign key errors?
Make sure these tables exist first:
- `classes` table
- `subjects` table
- `terms` table (created by the exam migration)

### Can't create exams?
You need at least one term in the `terms` table. See "Creating Your First Exam" above.

## Files Modified

- `/database/migrations/2026_02_14_072514_create_core_tables.php` - Added `hasTable()` checks
- `/database/schema/exam_tables.sql` - Manual SQL installation option
- This README file

## Next Steps

Consider adding:
1. A Terms management UI (currently missing)
2. Better error messages in the UI
3. Setup wizard for first-time installation
4. Data migration script from legacy tables if needed
