# Migration Rollback Guide

## Issue: "Migration not found" Error When Rolling Back

If you encounter this error when trying to rollback a specific migration:
```
2026_02_17_053600_add_emergency_contact_and_health_fields_to_admissions_table ... Migration not found
```

### Cause
This occurs when multiple migrations were run in the same batch, and you're trying to rollback only one of them using the `--path` option with a specific file.

Laravel's rollback command rolls back migrations by batch. When you use `--path` with a specific migration file, Laravel still tries to rollback all migrations in that batch, but can only find migration files within the specified path.

### Solution

#### Option 1: Rollback the Entire Batch (Recommended)
```bash
php artisan migrate:rollback
```
This will rollback all migrations in the last batch, including:
- `2026_02_17_053600_add_emergency_contact_and_health_fields_to_admissions_table`
- `2026_02_17_083400_create_hostel_management_tables`

#### Option 2: Rollback Using Directory Path
```bash
php artisan migrate:rollback --path=database/migrations
```

#### Option 3: Rollback By Steps
```bash
php artisan migrate:rollback --step=1
```

#### Option 4: Rollback Individual Migration (Using Custom Command)

If you need to rollback ONLY the hostel management tables, use the custom rollback command:

```bash
php artisan migrate:rollback-single 2026_02_17_083400_create_hostel_management_tables.php
```

This command will:
1. Check if the migration exists in the database
2. Show which batch it belongs to and warn if there are other migrations in the same batch
3. Ask for confirmation (unless `--force` is used)
4. Execute the migration's `down()` method
5. Remove it from the migrations table

**Force mode (skip confirmation):**
```bash
php artisan migrate:rollback-single 2026_02_17_083400_create_hostel_management_tables.php --force
```

⚠️ **Warning**: This breaks migration batch integrity. Use with caution and only when necessary.

#### Option 5: Manual Rollback (Advanced)

If you need complete control:

**Step 1**: Check which batch the migrations are in:
```bash
php artisan db
```
Then run:
```sql
SELECT * FROM migrations WHERE migration LIKE '%2026_02_17%' ORDER BY batch DESC;
```

**Step 2**: If both migrations are in the same batch and you only want to rollback the hostel migration:

```bash
# First, remove the hostel migration from the migrations table
php artisan db
```
```sql
DELETE FROM migrations WHERE migration = '2026_02_17_083400_create_hostel_management_tables';
```

**Step 3**: Then manually run the down() method:
```bash
php artisan tinker
```
```php
$migration = require 'database/migrations/2026_02_17_083400_create_hostel_management_tables.php';
$migration->down();
exit;
```

⚠️ **Warning**: Option 5 is not recommended as it breaks migration batch integrity and can cause issues with future migrations.

### Prevention

To avoid this issue in the future:

1. **Run migrations separately** if they need to be rolled back independently:
```bash
php artisan migrate --path=database/migrations/2026_02_17_053600_add_emergency_contact_and_health_fields_to_admissions_table.php
# Then later:
php artisan migrate --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
```

2. **Accept batch rollback**: If migrations are run together, they should be rolled back together.

### For Production

In production, the recommended approach is:

```bash
# Rollback the entire last batch
php artisan migrate:rollback

# Or rollback and re-run to refresh
php artisan migrate:refresh --step=1
```
