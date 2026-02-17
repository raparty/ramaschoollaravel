# Migration Rollback Examples

This document provides practical examples for rolling back migrations.

## Scenario 1: Rollback Individual Migration

When you need to rollback just the hostel management migration:

```bash
# Interactive mode (with confirmation)
php artisan migrate:rollback-single 2026_02_17_083400_create_hostel_management_tables.php

# The command will show:
# - Which batch the migration belongs to
# - Other migrations in the same batch (if any)
# - A warning about batch integrity
# - Ask for confirmation

# Force mode (skip confirmation - useful for scripts)
php artisan migrate:rollback-single 2026_02_17_083400_create_hostel_management_tables.php --force
```

## Scenario 2: Rollback Entire Batch

When you want to rollback all migrations that were run together:

```bash
# Rollback the last batch
php artisan migrate:rollback

# Rollback last 2 batches
php artisan migrate:rollback --step=2
```

## Scenario 3: Rollback with Path (Directory)

When you want to rollback migrations from a specific directory:

```bash
# Rollback migrations from database/migrations directory
php artisan migrate:rollback --path=database/migrations
```

⚠️ **Note**: Using `--path` with a specific file (not directory) will fail if other migrations in the same batch are in different files.

## Scenario 4: Check Migration Status

Before rolling back, check which migrations have been run:

```bash
php artisan migrate:status
```

## Scenario 5: Rollback and Re-run

If you want to rollback and immediately re-run migrations:

```bash
# Rollback and re-run the last batch
php artisan migrate:refresh --step=1

# Rollback all migrations and re-run
php artisan migrate:refresh
```

## Common Error: "Migration not found"

### Problem
```bash
$ php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php

2026_02_17_053600_add_emergency_contact_and_health_fields_to_admissions_table ... Migration not found
```

### Why This Happens
Laravel tries to rollback all migrations in the batch, but with `--path` pointing to a single file, it can't find other migrations in the same batch.

### Solution
Use one of these approaches:

**Option A: Use the custom command (recommended)**
```bash
php artisan migrate:rollback-single 2026_02_17_083400_create_hostel_management_tables.php
```

**Option B: Rollback entire batch**
```bash
php artisan migrate:rollback
```

**Option C: Use directory path**
```bash
php artisan migrate:rollback --path=database/migrations
```

## Production Considerations

### Before Rolling Back in Production

1. **Backup your database**
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Check migration status**
   ```bash
   php artisan migrate:status
   ```

3. **Review the down() method** of the migration you're rolling back to understand what will happen

4. **Consider data loss** - some migrations may drop tables or columns with data

### Safe Rollback in Production

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Backup database
mysqldump -u username -p database_name > backup.sql

# 3. Rollback migration
php artisan migrate:rollback-single your_migration.php

# 4. Verify the rollback
php artisan migrate:status

# 5. Test the application
# Run your tests or manual checks

# 6. Disable maintenance mode
php artisan up
```

## Troubleshooting

### Issue: Command not found
```bash
php artisan migrate:rollback-single
# Command "migrate:rollback-single" is not defined.
```

**Solution**: Make sure the custom command file exists:
```bash
ls -la app/Console/Commands/RollbackSingleMigration.php
```

If missing, the file needs to be created or restored from the repository.

### Issue: Database connection error
```bash
Could not connect to database: SQLSTATE[HY000] [2002] Connection refused
```

**Solution**: Check your `.env` file database configuration:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Issue: Migration already rolled back
```bash
Migration '2026_02_17_083400_create_hostel_management_tables' not found in database.
This migration has either not been run yet or has already been rolled back.
```

**Solution**: The migration is already rolled back. Check status with:
```bash
php artisan migrate:status
```
