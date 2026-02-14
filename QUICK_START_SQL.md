# Quick Start: Execute Missing Tables SQL

## Fastest Method - Copy & Paste

If you just want to quickly create all the missing tables, follow these steps:

### Method 1: MySQL Command Line (Recommended)
```bash
cd /path/to/ramaschoollaravel
mysql -u your_username -p your_database_name < database/schema/missing-tables.sql
```

### Method 2: Laravel Tinker (If MySQL CLI not available)
```bash
php artisan tinker
```
Then paste:
```php
DB::unprepared(file_get_contents('database/schema/missing-tables.sql'));
exit;
```

### Method 3: Direct SQL Execution
Connect to your MySQL database and execute:

```sql
-- Copy the entire content of database/schema/missing-tables.sql
-- and execute it in your MySQL client
```

---

## What Gets Created

After execution, you'll have these 5 new tables:

1. **exams** - Store examination details
2. **exam_subjects** - Link subjects to exams with marks
3. **results** - Store student exam results  
4. **staff_salaries** - Track staff salary payments
5. **grades** - Configure grading system (with 8 sample grades: A+ to F)

---

## Verify Installation

Check if tables were created:

```sql
SHOW TABLES LIKE 'exams';
SHOW TABLES LIKE 'exam_subjects';
SHOW TABLES LIKE 'results';
SHOW TABLES LIKE 'staff_salaries';
SHOW TABLES LIKE 'grades';
```

All 5 queries should return a table name.

Check if sample grades were inserted:
```sql
SELECT * FROM grades ORDER BY min_percentage DESC;
```

Should show 8 grades from A+ (90-100%) to F (0-32.99%).

---

## Test in Laravel

```php
php artisan tinker

// Test Exam model
\App\Models\Exam::count();

// Test Grade model
\App\Models\Grade::all();

// Should work without errors!
```

---

## Need Help?

See `MISSING_TABLES_README.md` for:
- Complete documentation
- Troubleshooting guide
- Alternative installation methods
- Table structure details

---

**That's it!** Your database now has all the tables needed for the Laravel models.
