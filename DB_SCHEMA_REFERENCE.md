# 📊 Database Schema, Tables & Columns Reference

> **Quick Answer:** All database schema files and documentation are in this repository!

---

## 🎯 Where to Find Schema Files

### 1. **Complete Production Database Schema**
📁 **Location:** `legacy_php/db/school_erp_schema_audit.sql`
- **What it contains:** Full database dump from production with ALL tables
- **Format:** SQL CREATE statements with complete column definitions
- **Size:** 23 KB
- **Tables:** 50+ tables including all legacy tables

**View it:**
```bash
cat legacy_php/db/school_erp_schema_audit.sql
```

### 2. **Laravel Migration Files**
📁 **Location:** `database/migrations/`
- **What it contains:** Laravel migration files defining tables
- **Format:** PHP Laravel migration syntax
- **Current migrations:**
  - `2026_02_14_072514_create_core_tables.php` - Core tables (16KB)
  - `2026_02_15_124200_create_leave_types_table.php` - Leave types
  - `2026_02_15_124300_create_staff_leaves_table.php` - Staff leaves
  - `2026_02_15_141000_create_transport_tables.php` - Transport tables

**View migrations:**
```bash
ls -lh database/migrations/
php artisan migrate:status  # See which migrations have run
```

### 3. **Missing Tables SQL (Ready to Execute)**
📁 **Location:** `database/schema/missing-tables.sql`
- **What it contains:** SQL to create 5 new tables (exams, exam_subjects, results, staff_salaries, grades)
- **Format:** Ready-to-execute SQL
- **Size:** 11 KB

**Execute it:**
```bash
mysql -u username -p database_name < database/schema/missing-tables.sql
```

### 4. **CREATE_MISSING_TABLES.sql** (Root Directory)
📁 **Location:** `CREATE_MISSING_TABLES.sql` (in root)
- **What it contains:** Alternative SQL script for missing tables
- **Format:** SQL CREATE statements
- **Size:** 6.7 KB

---

## 📚 Documentation Files

### **Main Index**
📄 **[DATABASE_DOCUMENTATION_INDEX.md](DATABASE_DOCUMENTATION_INDEX.md)**
- Complete guide to all database documentation
- Quick navigation to all schema resources
- 7 KB of organized links and explanations

### **Visual Reference**
📄 **[TABLES_VISUAL_REFERENCE.md](TABLES_VISUAL_REFERENCE.md)**
- ASCII art table diagrams
- Column descriptions with data types
- Relationship diagrams
- Sample data examples
- 14 KB of visual documentation

### **Quick Start**
📄 **[QUICK_START_SQL.md](QUICK_START_SQL.md)**
- Fast execution guide
- 3 different execution methods
- Verification queries
- 1.9 KB quick reference

### **Complete Guide**
📄 **[MISSING_TABLES_README.md](MISSING_TABLES_README.md)**
- Full installation instructions
- 4 installation methods
- Troubleshooting guide
- Testing procedures
- 9.4 KB comprehensive guide

### **Schema Fixes**
📄 **[SCHEMA_FIX_SUMMARY.md](SCHEMA_FIX_SUMMARY.md)**
- Documents 12 model fixes already applied
- Table name corrections
- Model-to-table mappings
- 8 KB of fix documentation

### **Module-Specific Documentation**
- 📄 **[TRANSPORT_MODULE_README.md](TRANSPORT_MODULE_README.md)** - Transport tables and schema (10 KB)
- 📄 **[STAFF_LEAVE_MANAGEMENT_README.md](STAFF_LEAVE_MANAGEMENT_README.md)** - Leave management tables (10 KB)
- 📄 **[SETTINGS_MODULE_README.md](SETTINGS_MODULE_README.md)** - Settings/RBAC tables (7 KB)

---

## 🗂️ All Database Tables Overview

### **Core Application Tables (from migrations)**

#### Student Management
- `admissions` - Student records
- `classes` - Class definitions
- `sections` - Section divisions
- `streams` - Academic streams
- `subjects` - Subject definitions
- `allocate_sections` - Class-section allocation
- `allocate_subjects` - Class-subject allocation

#### Attendance
- `attendance` - Student attendance records
- `staff_attendance` - Staff attendance records

#### Library
- `book_manager` - Book inventory
- `library_categories` - Book categories
- `student_books_details` - Book issue records
- `library_fines` - Library fine records

#### Fees Management
- `fee_packages` - Fee structures
- `fee_terms` - Fee payment terms
- `student_fees_detail` - Student fee records
- `student_transport_fees_detail` - Transport fee records

#### Exams & Results
- `exams` - Exam schedules
- `exam_subjects` - Exam-subject links
- `marks` - Student marks
- `results` - Compiled results
- `grades` - Grading system

#### Staff Management
- `staff_employee` - Staff records
- `staff_departments` - Departments
- `staff_positions` - Job positions
- `staff_attendance` - Staff attendance
- `staff_salaries` - Salary records
- `leave_types` - Leave type definitions
- `staff_leaves` - Leave applications

#### Transport Management
- `transport_add_route` - Transport routes
- `transport_add_vechile` - Vehicles (note: legacy spelling)
- `transport_student_detail` - Student transport assignments

#### Accounts & Finance
- `account_category` - Account categories
- `account_exp_income_detail` - Income/expense records
- `accounts` - Account transactions

#### System Tables
- `users` - User accounts
- `roles` - User roles (RBAC)
- `permissions` - System permissions
- `role_permissions` - Role-permission mappings
- `password_reset_tokens` - Password resets
- `failed_jobs` - Failed queue jobs
- `personal_access_tokens` - API tokens

---

## 🔍 How to Find Specific Table Information

### Method 1: Check Migration Files
```bash
# List all migrations
ls database/migrations/

# View a specific migration
cat database/migrations/2026_02_14_072514_create_core_tables.php
```

### Method 2: Check Schema SQL Files
```bash
# View production schema
cat legacy_php/db/school_erp_schema_audit.sql | grep "CREATE TABLE"

# Search for specific table
grep -A 20 "CREATE TABLE \`admissions\`" legacy_php/db/school_erp_schema_audit.sql
```

### Method 3: Use Laravel Artisan
```bash
# Show database tables
php artisan db:show

# Show specific table schema
php artisan db:table admissions

# Generate model from table
php artisan make:model ModelName --migration
```

### Method 4: Direct MySQL Query
```bash
# Show all tables
mysql -u username -p -e "SHOW TABLES;"

# Describe specific table
mysql -u username -p -e "DESCRIBE admissions;"

# Get CREATE statement
mysql -u username -p -e "SHOW CREATE TABLE admissions\G"
```

---

## 📋 Quick Column Reference for Common Tables

### `admissions` (Students)
```
id, reg_no, student_name, student_pic, dob, gender, blood_group,
class_id, admission_date, aadhaar_no, guardian_name, guardian_phone
```

### `staff_employee` (Staff)
```
id, employee_id, name, dept_id, cat_id, pos_id, qualification_id,
salary, joining_date, status
```

### `classes`
```
id, class_name, section, created_at, updated_at
```

### `subjects`
```
id, subject_name, subject_code
```

### `attendance`
```
id, student_id, date, status, remarks
```

### `exams`
```
id, name, class_id, session, start_date, end_date, total_marks,
pass_marks, is_published, description
```

### `transport_add_route`
```
route_id, route_name, cost, created_at
```

### `staff_leaves`
```
id, staff_id, leave_type_id, start_date, end_date, total_days,
reason, status, approved_by, approved_at
```

---

## 🚀 Quick Actions

### View All Tables in Database
```bash
mysql -u username -p database_name -e "SHOW TABLES;"
```

### Export Current Schema
```bash
mysqldump -u username -p database_name --no-data > current_schema.sql
```

### Import Missing Tables
```bash
mysql -u username -p database_name < database/schema/missing-tables.sql
```

### Check Laravel Models
```bash
# List all models
ls app/Models/

# Check model-table mapping
grep "protected \$table" app/Models/*.php
```

---

## 🎓 Understanding Table Naming Conventions

### Legacy Table Names (From Old PHP Code)
- `staff_employee` → Staff/employee data
- `book_manager` → Library books
- `library_categories` → Book categories
- `student_books_details` → Book issues
- `transport_add_route` → Transport routes
- `transport_add_vechile` → Vehicles (note spelling)

### Laravel Conventions (New Tables)
- Plural snake_case: `admissions`, `classes`, `sections`
- Junction tables: `allocate_sections`, `allocate_subjects`
- Relationship tables: `role_permissions`

### Model to Table Mappings
Most models explicitly define their table names:

```php
// Staff.php
protected $table = 'staff_employee';

// Book.php
protected $table = 'book_manager';

// TransportVehicle.php
protected $table = 'transport_add_vechile';
```

Check `app/Models/*.php` files for exact mappings.

---

## 💡 Pro Tips

### 1. **Always Check Documentation First**
Start with [DATABASE_DOCUMENTATION_INDEX.md](DATABASE_DOCUMENTATION_INDEX.md) - it has everything organized.

### 2. **Use Visual Reference for Quick Lookup**
[TABLES_VISUAL_REFERENCE.md](TABLES_VISUAL_REFERENCE.md) has ASCII diagrams - perfect for quick reference.

### 3. **Check Model Files for Relationships**
```bash
# See all model relationships
grep -r "public function.*belongsTo\|hasMany\|hasOne" app/Models/
```

### 4. **Migration Files Are Source of Truth**
For Laravel-managed tables, migrations are the definitive schema source:
```bash
cat database/migrations/*.php
```

### 5. **Legacy SQL Files for Production**
For legacy tables, check `legacy_php/db/school_erp_schema_audit.sql` - it's the production dump.

---

## 🆘 Common Questions

### Q: "I need to see all columns for a table"
**A:** Three options:
1. Check migration file in `database/migrations/`
2. Check `legacy_php/db/school_erp_schema_audit.sql`
3. Run: `mysql -e "DESCRIBE table_name;"`

### Q: "Which tables are missing?"
**A:** Read [MISSING_TABLES_README.md](MISSING_TABLES_README.md) or execute:
```bash
diff <(grep "class.*extends Model" app/Models/*.php | cut -d: -f1 | xargs -I{} basename {} .php) \
     <(mysql -sN -e "SHOW TABLES;")
```

### Q: "How do I add a new table?"
**A:** Create a migration:
```bash
php artisan make:migration create_tablename_table
# Edit the migration file
php artisan migrate
```

### Q: "Where's the ER diagram?"
**A:** Check [TABLES_VISUAL_REFERENCE.md](TABLES_VISUAL_REFERENCE.md) for ASCII relationship diagrams.

### Q: "I need the complete schema as SQL"
**A:** Use one of these:
- `legacy_php/db/school_erp_schema_audit.sql` (production dump)
- `database/schema/missing-tables.sql` (new tables only)
- `mysqldump -u user -p database --no-data` (current state)

---

## 📞 Getting More Help

### File Structure Summary
```
ramaschoollaravel/
├── 📄 DATABASE_DOCUMENTATION_INDEX.md    ← START HERE
├── 📄 DB_SCHEMA_REFERENCE.md            ← This file
├── 📄 TABLES_VISUAL_REFERENCE.md        ← Visual diagrams
├── 📄 QUICK_START_SQL.md                ← Quick execution
├── 📄 MISSING_TABLES_README.md          ← Installation guide
├── 📄 SCHEMA_FIX_SUMMARY.md             ← Model fixes
├── 📁 database/
│   ├── migrations/                      ← Laravel migrations
│   └── schema/
│       └── missing-tables.sql           ← Missing tables SQL
├── 📁 legacy_php/db/
│   └── school_erp_schema_audit.sql      ← Full production schema
└── 📁 app/Models/                        ← Check for table mappings
```

### Documentation Sizes
- Complete schema SQL: **23 KB** (`school_erp_schema_audit.sql`)
- Core migrations: **16 KB** (`create_core_tables.php`)
- Missing tables SQL: **11 KB** (`missing-tables.sql`)
- Total documentation: **50+ KB** across 8+ files

---

## ✅ Summary

**You have comprehensive database documentation!**

### Schema Files:
1. ✅ Production schema: `legacy_php/db/school_erp_schema_audit.sql`
2. ✅ Laravel migrations: `database/migrations/*.php`
3. ✅ Missing tables SQL: `database/schema/missing-tables.sql`
4. ✅ Quick SQL: `CREATE_MISSING_TABLES.sql`

### Documentation:
1. ✅ Main index: `DATABASE_DOCUMENTATION_INDEX.md`
2. ✅ Visual reference: `TABLES_VISUAL_REFERENCE.md`
3. ✅ Quick start: `QUICK_START_SQL.md`
4. ✅ Complete guide: `MISSING_TABLES_README.md`
5. ✅ Schema fixes: `SCHEMA_FIX_SUMMARY.md`
6. ✅ This reference: `DB_SCHEMA_REFERENCE.md`

### Total Coverage:
- **50+ tables** documented
- **23 KB** of schema SQL
- **50+ KB** of documentation
- **Multiple formats** (SQL, PHP migrations, markdown docs)

---

**Everything is organized and ready to use! 🚀**

Start with [DATABASE_DOCUMENTATION_INDEX.md](DATABASE_DOCUMENTATION_INDEX.md) for the complete overview.

---

*Last Updated: February 15, 2026*  
*Laravel Version: 10.x*  
*Database: MySQL 5.7+ / MariaDB 10.2+*
