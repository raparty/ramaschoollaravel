# Missing Database Tables - SQL Creation Guide

## Overview
This document explains how to create the missing database tables for Laravel models that currently don't have corresponding tables in the database.

## Missing Tables

The following 5 models have been identified as missing their database tables:

### 1. **Exam Model** (`app/Models/Exam.php`)
- **Table:** `exams`
- **Purpose:** Manages examination schedules and details (Midterm, Final, Unit Tests, etc.)
- **Key Features:**
  - Exam name and session tracking
  - Date ranges (start_date, end_date)
  - Total marks and pass marks configuration
  - Result publication status
  - Soft delete support

### 2. **ExamSubject Model** (`app/Models/ExamSubject.php`)
- **Table:** `exam_subjects`
- **Purpose:** Links subjects to exams with marks configuration
- **Key Features:**
  - Theory and practical marks allocation
  - Subject-specific pass marks
  - Exam date and time scheduling
  - Duration tracking in minutes

### 3. **Result Model** (`app/Models/Result.php`)
- **Table:** `results`
- **Purpose:** Stores compiled student results for exams
- **Key Features:**
  - Total marks obtained and percentage calculation
  - Grade assignment (A+, A, B, etc.)
  - Class rank tracking
  - Pass/Fail status
  - Result publication control

### 4. **Salary Model** (`app/Models/Salary.php`)
- **Table:** `staff_salaries`
- **Purpose:** Manages staff salary payments and records
- **Key Features:**
  - Monthly salary tracking
  - Basic salary, allowances, and deductions
  - Net salary calculation
  - Payment status (pending/paid)
  - Payment method and date tracking

### 5. **Grade Model** (`app/Models/Grade.php`)
- **Table:** `grades`
- **Purpose:** Defines grading system configuration
- **Key Features:**
  - Grade definitions (A+, A, B+, B, C, D, F)
  - Percentage ranges for each grade
  - Grade points for GPA calculation
  - Includes sample data with standard grading scale

## Installation Instructions

### Step 1: Backup Your Database
**IMPORTANT:** Always backup your database before making schema changes.

```bash
# Using mysqldump
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Or use your hosting provider's backup tools
```

### Step 2: Execute the SQL File

#### Option A: Using MySQL Command Line
```bash
mysql -u username -p database_name < database/schema/missing-tables.sql
```

#### Option B: Using MySQL Workbench
1. Open MySQL Workbench
2. Connect to your database
3. File → Open SQL Script
4. Select `database/schema/missing-tables.sql`
5. Execute the script (⚡ icon or Ctrl+Shift+Enter)

#### Option C: Using phpMyAdmin
1. Log in to phpMyAdmin
2. Select your database
3. Click on "SQL" tab
4. Copy contents of `database/schema/missing-tables.sql`
5. Paste and click "Go"

#### Option D: Using Laravel Tinker
```bash
php artisan tinker
```
```php
DB::unprepared(file_get_contents('database/schema/missing-tables.sql'));
```

### Step 3: Verify Table Creation
```sql
-- Check if all tables were created
SHOW TABLES LIKE 'exams';
SHOW TABLES LIKE 'exam_subjects';
SHOW TABLES LIKE 'results';
SHOW TABLES LIKE 'staff_salaries';
SHOW TABLES LIKE 'grades';

-- Verify table structure
DESCRIBE exams;
DESCRIBE exam_subjects;
DESCRIBE results;
DESCRIBE staff_salaries;
DESCRIBE grades;

-- Check sample grades data
SELECT * FROM grades ORDER BY min_percentage DESC;
```

## Database Schema Details

### Exams Table Structure
```
exams (
  id                INT PRIMARY KEY AUTO_INCREMENT
  name              VARCHAR(255) NOT NULL
  class_id          INT NOT NULL
  session           VARCHAR(50) NOT NULL
  start_date        DATE NOT NULL
  end_date          DATE NOT NULL
  total_marks       INT DEFAULT 100
  pass_marks        INT DEFAULT 40
  is_published      TINYINT(1) DEFAULT 0
  description       TEXT
  created_at        TIMESTAMP
  updated_at        TIMESTAMP
  deleted_at        TIMESTAMP (for soft deletes)
  
  Foreign Key: class_id → classes(id)
)
```

### Exam Subjects Table Structure
```
exam_subjects (
  id                INT PRIMARY KEY AUTO_INCREMENT
  exam_id           INT NOT NULL
  subject_id        INT NOT NULL
  theory_marks      INT DEFAULT 70
  practical_marks   INT DEFAULT 30
  pass_marks        INT DEFAULT 40
  exam_date         DATE
  exam_time         TIME
  duration_minutes  INT DEFAULT 180
  created_at        TIMESTAMP
  updated_at        TIMESTAMP
  
  Foreign Keys: 
    exam_id → exams(id)
    subject_id → subjects(id)
  Unique: (exam_id, subject_id)
)
```

### Results Table Structure
```
results (
  id                      INT PRIMARY KEY AUTO_INCREMENT
  student_id              INT NOT NULL
  exam_id                 INT NOT NULL
  total_marks_obtained    DECIMAL(10,2) DEFAULT 0.00
  total_max_marks         INT NOT NULL
  percentage              DECIMAL(5,2) DEFAULT 0.00
  grade                   VARCHAR(10)
  rank                    INT
  is_passed               TINYINT(1) DEFAULT 0
  is_published            TINYINT(1) DEFAULT 0
  remarks                 TEXT
  created_at              TIMESTAMP
  updated_at              TIMESTAMP
  
  Foreign Keys:
    student_id → admissions(id)
    exam_id → exams(id)
  Unique: (student_id, exam_id)
)
```

### Staff Salaries Table Structure
```
staff_salaries (
  id              INT PRIMARY KEY AUTO_INCREMENT
  staff_id        INT NOT NULL
  month           TINYINT NOT NULL (1-12)
  year            YEAR NOT NULL
  basic_salary    DECIMAL(10,2) DEFAULT 0.00
  allowances      DECIMAL(10,2) DEFAULT 0.00
  deductions      DECIMAL(10,2) DEFAULT 0.00
  net_salary      DECIMAL(10,2) DEFAULT 0.00
  status          ENUM('pending','paid') DEFAULT 'pending'
  payment_date    DATE
  payment_method  VARCHAR(50)
  notes           TEXT
  created_at      TIMESTAMP
  updated_at      TIMESTAMP
  
  Foreign Key: staff_id → staff_employee(id)
  Unique: (staff_id, month, year)
)
```

### Grades Table Structure
```
grades (
  id              INT PRIMARY KEY AUTO_INCREMENT
  grade           VARCHAR(10) NOT NULL UNIQUE
  min_percentage  DECIMAL(5,2) NOT NULL
  max_percentage  DECIMAL(5,2) NOT NULL
  points          DECIMAL(3,2) DEFAULT 0.00
  description     TEXT
  created_at      TIMESTAMP
  updated_at      TIMESTAMP
)
```

## Sample Grading System

The SQL file includes a standard grading system:

| Grade | Range | Points | Description |
|-------|-------|--------|-------------|
| A+ | 90-100% | 4.00 | Outstanding |
| A | 80-89.99% | 3.70 | Excellent |
| B+ | 70-79.99% | 3.30 | Very Good |
| B | 60-69.99% | 3.00 | Good |
| C+ | 50-59.99% | 2.70 | Above Average |
| C | 40-49.99% | 2.00 | Average |
| D | 33-39.99% | 1.00 | Pass |
| F | 0-32.99% | 0.00 | Fail |

**Note:** You can modify these ranges in the SQL file according to your institution's grading policy.

## Foreign Key Dependencies

The new tables depend on existing tables:
- `exams.class_id` → `classes.id`
- `exam_subjects.exam_id` → `exams.id`
- `exam_subjects.subject_id` → `subjects.id`
- `results.student_id` → `admissions.id`
- `results.exam_id` → `exams.id`
- `staff_salaries.staff_id` → `staff_employee.id`

Ensure these referenced tables exist before executing the SQL file.

## Testing After Installation

### Test Exam Functionality
```php
php artisan tinker

// Create a test exam
$exam = \App\Models\Exam::create([
    'name' => 'Mid-Term Exam 2024',
    'class_id' => 1,
    'session' => '2023-2024',
    'start_date' => '2024-03-01',
    'end_date' => '2024-03-15',
    'total_marks' => 500,
    'pass_marks' => 200,
]);

// Verify
\App\Models\Exam::count(); // Should return 1 or more
```

### Test Grade Functionality
```php
php artisan tinker

// Get all grades
\App\Models\Grade::all();

// Find grade for a percentage
\App\Models\Grade::forPercentage(85.5);
```

## Troubleshooting

### Issue: Foreign Key Constraint Fails
**Solution:** Ensure the referenced tables exist:
```sql
SELECT TABLE_NAME FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'your_database_name' 
AND TABLE_NAME IN ('classes', 'subjects', 'admissions', 'staff_employee');
```

### Issue: Table Already Exists
**Solution:** The SQL uses `IF NOT EXISTS` clause, so it's safe to re-run. To force recreation:
```sql
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS exam_subjects;
DROP TABLE IF EXISTS exams;
DROP TABLE IF EXISTS staff_salaries;
DROP TABLE IF EXISTS grades;
-- Then re-run the missing-tables.sql file
```

### Issue: Character Set/Collation Mismatch
**Solution:** Verify your database charset:
```sql
SHOW CREATE DATABASE your_database_name;
```
If different from utf8mb4, modify the SQL file accordingly.

## Additional Notes

1. **Indexes:** The tables include appropriate indexes for performance optimization
2. **Constraints:** Foreign keys are set with CASCADE delete for data integrity
3. **Timestamps:** All tables use Laravel-standard `created_at` and `updated_at` columns
4. **Soft Deletes:** The `exams` table includes `deleted_at` for soft deletion support

## Next Steps After Installation

Once tables are created, you can:
1. Create Exam Management interface
2. Build Result Compilation system
3. Implement Staff Salary Management
4. Use the Grade system for automatic grade assignment

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection in `.env` file
3. Run migrations: `php artisan migrate`
4. Clear cache: `php artisan cache:clear`

---

**Last Updated:** February 14, 2026  
**Compatible With:** MySQL 5.7+, MariaDB 10.2+  
**Laravel Version:** 9.x+
