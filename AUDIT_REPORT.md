# 🔍 COMPREHENSIVE DATABASE AUDIT REPORT
**Date:** February 15, 2026  
**Project:** Rama School Laravel ERP  
**Task:** Strict Audit and Repair Mode

---

## 📊 EXECUTIVE SUMMARY

**Total Models Analyzed:** 28  
**Total Controllers Analyzed:** 24  
**Total Blade Views:** 79 files  
**Critical Issues Found:** 12  
**Schema Mismatches:** 8 major, 15 column-level  
**Missing Tables:** 3 (results, staff_salaries, grades)  
**Broken Modules:** 4 (Exams, Marks, Results, Salaries)

---

## 🚨 PART A: CRITICAL DATABASE MISMATCHES

### 1. **EXAM MODEL - MAJOR SCHEMA MISMATCH** ⛔

**Severity:** CRITICAL - Will cause 500 errors on exam creation/editing

| Component | Expected by Model/Code | Actual in Migration | Status |
|-----------|----------------------|---------------------|--------|
| **Table Name** | `exams` | `exams` | ✅ Match |
| **Primary Key** | `class_id` (FK to classes) | `term_id` (FK to terms) | ❌ **MISMATCH** |
| **Session Field** | `session` (string, e.g., "2023-2024") | NOT EXISTS | ❌ **MISSING** |
| **Marks Config** | `total_marks`, `pass_marks` (integers) | NOT EXISTS | ❌ **MISSING** |
| **Publishing** | `is_published` (boolean) | NOT EXISTS | ❌ **MISSING** |
| **Description** | `description` (text, nullable) | NOT EXISTS | ❌ **MISSING** |
| **Soft Deletes** | `deleted_at` | NOT EXISTS | ❌ **MISSING** |

**Migration Schema (Lines 273-280):**
```php
Schema::create('exams', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('term_id')->constrained()->onDelete('cascade');  // NOT class_id
    $table->date('start_date');
    $table->date('end_date');
    $table->timestamps();  // Missing: session, total_marks, pass_marks, is_published
});
```

**Model Expectation (app/Models/Exam.php:39-49):**
```php
protected $fillable = [
    'name', 'class_id', 'session', 'start_date', 'end_date',
    'total_marks', 'pass_marks', 'is_published', 'description'
];
```

**Impact:**
- ❌ ExamController::create() will fail - tries to save `class_id` and `session`
- ❌ ExamController::togglePublish() will fail - column `is_published` doesn't exist
- ❌ All exam forms will break - form fields expect non-existent columns
- ❌ Exam relationships broken - `$exam->class` relationship fails

**Files Affected:**
- `app/Http/Controllers/ExamController.php` (Lines 56, 103, 113, 238, 243)
- `resources/views/exams/create.blade.php` (Lines 40, 55, 107-180)
- `resources/views/exams/show.blade.php` (Lines 10, 42, 60)
- `resources/views/exams/index.blade.php` (Lines 28, 36, 57, 100, 104-105, 111, 114, 140)

---

### 2. **EXAM_SUBJECT MODEL - SCHEMA MISMATCH** ⚠️

**Severity:** HIGH - Marks entry will fail

| Component | Expected by Model | Actual in Migration | Status |
|-----------|------------------|---------------------|--------|
| **Marks Structure** | `theory_marks` + `practical_marks` | `max_marks` (single field) | ❌ **MISMATCH** |
| **Subject Linking** | `subject_id` only | `exam_id`, `class_id`, `subject_id` | ⚠️ Extra field |
| **Scheduling** | `exam_date`, `exam_time`, `duration_minutes` | NOT EXISTS | ❌ **MISSING** |

**Migration Schema (Lines 282-292):**
```php
Schema::create('exam_subjects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_id')->constrained()->onDelete('cascade');
    $table->foreignId('class_id')->constrained()->onDelete('cascade');
    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    $table->integer('max_marks');      // NOT theory_marks + practical_marks
    $table->integer('pass_marks');
    $table->timestamps();
    // Missing: exam_date, exam_time, duration_minutes
});
```

**Model Expectation (app/Models/ExamSubject.php:36-45):**
```php
protected $fillable = [
    'exam_id', 'subject_id',
    'theory_marks', 'practical_marks',  // Migration uses 'max_marks'
    'pass_marks',
    'exam_date', 'exam_time', 'duration_minutes',  // These don't exist
];
```

**Impact:**
- ❌ ExamController::storeSubjects() will fail - tries to save theory/practical marks
- ❌ Timetable generation broken - no exam_date/exam_time fields
- ❌ Views expect practical marks separation

**Files Affected:**
- `app/Http/Controllers/ExamController.php` (Lines 199-200)
- `resources/views/exams/subjects.blade.php`
- `resources/views/exams/timetable.blade.php`

---

### 3. **MARK MODEL - COMPLETE SCHEMA MISMATCH** 🔴

**Severity:** CRITICAL - Marks module completely broken

| Component | Expected by Model | Actual in Migration | Status |
|-----------|------------------|---------------------|--------|
| **Table Name** | `marks` | `student_marks` | ❌ **WRONG TABLE** |
| **Student Link** | `student_id` (to User) | `admission_id` (to Admission) | ❌ **MISMATCH** |
| **Exam Link** | `subject_name`, `exam_type` | `exam_subject_id` (FK) | ❌ **COMPLETELY DIFFERENT** |
| **Marks Storage** | `marks_obtained`, `total_marks` | `marks_obtained`, `grade`, `is_absent` | ⚠️ Partial |
| **Academic Year** | `academic_year` (string) | NOT EXISTS | ❌ **MISSING** |
| **Timestamps** | `public $timestamps = false` | Has timestamps | ❌ **MISMATCH** |

**Migration Schema (Lines 294-302):**
```php
Schema::create('student_marks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admission_id')->constrained()->onDelete('cascade');  // NOT student_id
    $table->foreignId('exam_subject_id')->constrained()->onDelete('cascade');  // NOT subject_name/exam_type
    $table->decimal('marks_obtained', 5, 2);
    $table->enum('grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'])->nullable();
    $table->boolean('is_absent')->default(false);
    $table->timestamps();  // Model has $timestamps = false
});
```

**Model Expectation (app/Models/Mark.php:31-38):**
```php
protected $fillable = [
    'student_id',         // Migration uses admission_id
    'subject_name',       // Migration uses exam_subject_id FK
    'exam_type',          // Migration doesn't have this
    'marks_obtained',     // ✓ Exists
    'total_marks',        // Migration doesn't have this
    'academic_year',      // Migration doesn't have this
];
```

**Impact:**
- ❌ ALL mark queries will fail - wrong table name
- ❌ ALL mark relationships broken - wrong foreign keys
- ❌ MarkController::store() completely broken
- ❌ All marks views will error out
- ❌ Student marks history won't load
- ❌ Mark entry forms won't work

**Files Affected:**
- `app/Models/Mark.php` - Entire model needs rewrite
- `app/Http/Controllers/MarkController.php` - All methods (Lines 45, 77, 95, 97, 131)
- `resources/views/marks/entry.blade.php` (Lines 94, 100-122)
- `resources/views/marks/index.blade.php` (Lines 146, 150-194)
- `resources/views/marks/student.blade.php` - All references
- `resources/views/marks/subject.blade.php` - All references

---

### 4. **RESULT MODEL - TABLE DOESN'T EXIST** 🔴

**Severity:** CRITICAL - Results module completely non-functional

**Issue:** The `Result` model expects a `results` table that **DOES NOT EXIST** in the migration.

**Evidence:**
- Migration file: NO `results` table created (verified in all 30 tables)
- SQL available: `database/schema/missing-tables.sql` contains CREATE TABLE for results
- Controller: `ResultController.php` tries to query non-existent table
- Routes: Results routes defined but will all 500 error

**Required Columns (from missing-tables.sql:80-103):**
```sql
CREATE TABLE IF NOT EXISTS `results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `exam_id` INT NOT NULL,
  `total_marks_obtained` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_max_marks` INT NOT NULL,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `grade` VARCHAR(10) NULL,
  `rank` INT NULL,
  `is_passed` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_exam` (`student_id`, `exam_id`),
  CONSTRAINT `fk_results_student` FOREIGN KEY (`student_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
);
```

**Impact:**
- ❌ ResultController::index() - fails on Result::query()
- ❌ ResultController::generate() - fails on Result::create()
- ❌ ResultController::view() - fails on Result::find()
- ❌ All result routes return 500 errors
- ❌ Dashboard might error if it queries results

**Files Affected:**
- `app/Http/Controllers/ResultController.php` - All methods
- `resources/views/results/*.blade.php` - All 3 files
- `routes/web.php` - Lines 133-140

---

### 5. **SALARY MODEL - TABLE DOESN'T EXIST** 🔴

**Severity:** CRITICAL - Salary module completely non-functional

**Issue:** The `Salary` model explicitly uses `staff_salaries` table (Line 37) which **DOES NOT EXIST** in migration.

**Model Declaration:**
```php
protected $table = 'staff_salaries';  // This table doesn't exist!
```

**Required Columns (from missing-tables.sql:112-134):**
```sql
CREATE TABLE IF NOT EXISTS `staff_salaries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `staff_id` INT NOT NULL,
  `month` TINYINT NOT NULL,
  `year` YEAR NOT NULL,
  `basic_salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `allowances` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `deductions` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
  `payment_date` DATE NULL,
  `payment_method` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_month_year` (`staff_id`, `month`, `year`),
  CONSTRAINT `fk_salaries_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff_employee` (`id`) ON DELETE CASCADE
);
```

**Impact:**
- ❌ SalaryController::index() - fails on Salary::query()
- ❌ SalaryController::store() - fails on Salary::create()
- ❌ SalaryController::markAsPaid() - fails on salary update
- ❌ All salary routes return 500 errors
- ❌ Staff history page broken

**Files Affected:**
- `app/Http/Controllers/SalaryController.php` - All methods
- `resources/views/salaries/*.blade.php` - All files
- `routes/web.php` - Lines 90-98

---

### 6. **GRADE MODEL - TABLE DOESN'T EXIST** ⚠️

**Severity:** MEDIUM - Grade system non-functional, but may not be heavily used

**Issue:** The `Grade` model expects a `grades` table that doesn't exist in migration.

**SQL Available:** `database/schema/missing-tables.sql:143-177` with sample grading data

**Impact:**
- Grade-based result compilation won't work
- Manual grade determination in Result model (lines 98-118) will be used instead
- GPA calculation not possible

---

### 7. **STUDENT_FEES TABLE NAME DISCREPANCY** ⚠️

**Issue:** Migration creates `student_fees` but model uses `student_fees_detail`

**Migration (actual table name):**
```php
Schema::create('student_fees', function (Blueprint $table) {
```

**Model Declaration (app/Models/StudentFee.php:30):**
```php
protected $table = 'student_fees_detail';  // Different from migration!
```

**Status:** ⚠️ May be intentional if database was manually modified, but creates confusion

---

### 8. **STAFF TABLE NAME DISCREPANCY** ⚠️

**Issue:** Migration creates `staff` but model uses `staff_employee`

**Migration (actual table name):**
```php
Schema::create('staff', function (Blueprint $table) {
```

**Model Declaration (app/Models/Staff.php):**
```php
protected $table = 'staff_employee';  // Different from migration!
```

**Status:** ⚠️ May be intentional if database was manually modified

---

## 📋 PART B: MISSING MODULE FUNCTIONALITY

### 1. Examination Module - 60% Broken

**Working:**
- ✅ Exam listing (if schema fixed)
- ✅ Basic CRUD operations (if schema fixed)

**Broken:**
- ❌ Exam creation/editing - wrong columns
- ❌ Subject assignment - schema mismatch
- ❌ Timetable generation - missing columns
- ❌ Publish/unpublish - column doesn't exist
- ❌ Session-based filtering - field doesn't exist

### 2. Marks Entry Module - 100% Broken

**All Broken:**
- ❌ Mark entry form
- ❌ Mark storage
- ❌ Student marks history
- ❌ Subject-wise marks
- ❌ Mark listing/search

**Root Cause:** Complete schema mismatch between model and migration

### 3. Results Module - 100% Non-Functional

**All Broken:**
- ❌ Result generation
- ❌ Result viewing
- ❌ Result publishing
- ❌ Class results
- ❌ Result marksheet

**Root Cause:** Table doesn't exist

### 4. Salary Module - 100% Non-Functional

**All Broken:**
- ❌ Salary processing
- ❌ Salary listing
- ❌ Salary payment
- ❌ Salary slip generation
- ❌ Staff salary history
- ❌ Bulk salary generation

**Root Cause:** Table doesn't exist

### 5. Grading System - Non-Functional

**Impact:**
- Results must use hardcoded grade determination
- No GPA calculation
- No institutional grading customization

---

## 🔗 PART C: BROKEN ROUTES & PAGES

### Critical Routes (Will 500 Error)

#### Examination Routes:
```
POST   /exams                     → ExamController@store (column mismatch)
GET    /exams/{exam}              → ExamController@show (relationship broken)
PUT    /exams/{exam}              → ExamController@update (column mismatch)
POST   /exams/{exam}/subjects     → ExamController@storeSubjects (column mismatch)
POST   /exams/{exam}/toggle-publish → ExamController@togglePublish (column doesn't exist)
GET    /exams/{exam}/timetable    → ExamController@timetable (columns missing)
```

#### Marks Routes (ALL BROKEN):
```
GET    /marks                     → MarkController@index (table name wrong)
GET    /marks/entry               → MarkController@entryForm (schema mismatch)
POST   /marks/store               → MarkController@store (columns wrong)
GET    /marks/student             → MarkController@studentMarks (columns wrong)
GET    /marks/subject/{examSubject} → MarkController@subjectMarks (columns wrong)
```

#### Results Routes (ALL BROKEN):
```
GET    /results                   → ResultController@index (table doesn't exist)
GET    /results/generate          → ResultController@generateForm (table doesn't exist)
POST   /results/generate          → ResultController@generate (table doesn't exist)
GET    /results/{result}          → ResultController@view (table doesn't exist)
POST   /results/{result}/toggle-publish → ResultController@togglePublish (table doesn't exist)
```

#### Salary Routes (ALL BROKEN):
```
GET    /salaries                  → SalaryController@index (table doesn't exist)
GET    /salaries/process          → SalaryController@process (table doesn't exist)
POST   /salaries/store            → SalaryController@store (table doesn't exist)
POST   /salaries/generate-bulk    → SalaryController@generateBulk (table doesn't exist)
POST   /salaries/{salary}/mark-paid → SalaryController@markAsPaid (table doesn't exist)
GET    /salaries/{salary}/slip    → SalaryController@slip (table doesn't exist)
GET    /salaries/staff/{staff}/history → SalaryController@history (table doesn't exist)
```

### Pages Expected to 500 Error:

1. **Exam Management:**
   - Create Exam page (form submission)
   - Edit Exam page (form submission)
   - Exam detail page (if relationships loaded)
   - Subject assignment page

2. **Marks Entry:**
   - ALL marks pages (100% broken)

3. **Results:**
   - ALL results pages (100% broken)

4. **Salaries:**
   - ALL salary pages (100% broken)

5. **Dashboard:**
   - May partially work, but some stats might error

---

## ⚠️ PART D: 500 ERROR ROOT CAUSES

### Category 1: Non-Existent Columns

**Query Pattern:**
```php
Exam::create([
    'class_id' => $request->class_id,  // Column doesn't exist
    'session' => $request->session,    // Column doesn't exist
    'is_published' => false,           // Column doesn't exist
]);
```

**Error:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'class_id' in 'field list'
```

**Affected Files:**
- ExamController.php (create, update, togglePublish)
- MarkController.php (all methods)
- Views referencing non-existent columns

### Category 2: Non-Existent Tables

**Query Pattern:**
```php
Result::query()->get();  // Table 'results' doesn't exist
Salary::query()->get();  // Table 'staff_salaries' doesn't exist
```

**Error:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'database.results' doesn't exist
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'database.staff_salaries' doesn't exist
```

**Affected:**
- ResultController.php (all methods)
- SalaryController.php (all methods)

### Category 3: Wrong Table Name

**Query Pattern:**
```php
Mark::query()->get();  // Model expects 'marks', migration created 'student_marks'
```

**Error:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'database.marks' doesn't exist
```

**Affected:**
- MarkController.php (all methods)
- Any view with marks relationships

### Category 4: Broken Relationships

**Query Pattern:**
```php
$exam->class;  // BelongsTo relationship using 'class_id' which doesn't exist
```

**Error:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'class_id' in 'where clause'
```

**Affected:**
- Exam views loading class relationship
- Mark model loading student relationship

### Category 5: Mass Assignment Exceptions

**Query Pattern:**
```php
Exam::create($request->all());  // Includes fields not in fillable/database
```

**Error:**
```
Illuminate\Database\QueryException: SQLSTATE[42S22]: Column not found
```

**Affected:**
- All create/update operations with mismatched columns

---

## 📊 SEVERITY MATRIX

| Module | Severity | Broken % | Root Cause | Fix Complexity |
|--------|----------|----------|------------|----------------|
| **Marks** | 🔴 CRITICAL | 100% | Wrong table name + schema | HIGH |
| **Results** | 🔴 CRITICAL | 100% | Table doesn't exist | MEDIUM |
| **Salaries** | 🔴 CRITICAL | 100% | Table doesn't exist | MEDIUM |
| **Exams** | 🟠 HIGH | 60% | Column mismatches | HIGH |
| **ExamSubjects** | 🟡 MEDIUM | 40% | Column mismatches | MEDIUM |
| **Grades** | 🟡 MEDIUM | 100% | Table doesn't exist | LOW |

---

## 🎯 RECOMMENDED FIX STRATEGY

### Phase 1: Database Schema Fixes (CRITICAL)

**Do NOT add new tables or modify existing structure per requirements.**
**ONLY fix code to match existing schema.**

### Phase 2: Model Corrections

1. **Fix Exam Model** → Match migration schema (use `term_id` not `class_id`)
2. **Fix ExamSubject Model** → Use `max_marks` not theory/practical split
3. **Fix Mark Model** → Use `student_marks` table, `admission_id`, `exam_subject_id`
4. **Disable Result Module** → Table doesn't exist, code will fail
5. **Disable Salary Module** → Table doesn't exist, code will fail
6. **Disable Grade Module** → Table doesn't exist

### Phase 3: Controller Corrections

1. Fix ExamController to match schema
2. Fix MarkController to match schema
3. Comment out or remove ResultController references
4. Comment out or remove SalaryController references

### Phase 4: View Corrections

1. Fix exam views to use `term_id` and remove non-existent columns
2. Fix marks views to use correct table structure
3. Remove or comment out result views
4. Remove or comment out salary views

### Phase 5: Route Corrections

1. Comment out result routes
2. Comment out salary routes
3. Update route model binding if needed

---

## 📝 DETAILED COLUMN MAPPING

### Exam Model Fix Required:

| Current (Model) | Migration Schema | Action |
|----------------|------------------|--------|
| `class_id` | `term_id` | REPLACE with term_id |
| `session` | NOT EXISTS | REMOVE from model |
| `total_marks` | NOT EXISTS | REMOVE from model |
| `pass_marks` | NOT EXISTS | REMOVE from model |
| `is_published` | NOT EXISTS | REMOVE from model |
| `description` | NOT EXISTS | REMOVE from model |
| `deleted_at` | NOT EXISTS | REMOVE SoftDeletes trait |

### Mark Model Fix Required:

| Current (Model) | Migration Schema | Action |
|----------------|------------------|--------|
| Table: `marks` | Table: `student_marks` | UPDATE table name |
| `student_id` | `admission_id` | REPLACE column |
| `subject_name` | `exam_subject_id` | REPLACE with FK |
| `exam_type` | NOT EXISTS | REMOVE |
| `total_marks` | NOT EXISTS | REMOVE |
| `academic_year` | NOT EXISTS | REMOVE |
| NO grade | `grade` | ADD column |
| NO is_absent | `is_absent` | ADD column |
| timestamps=false | timestamps=true | ENABLE timestamps |

---

## 🔧 FILES REQUIRING CHANGES

### High Priority (Causing 500 Errors):

**Models (6 files):**
1. `app/Models/Exam.php` - Rewrite fillable, remove soft deletes, fix relationships
2. `app/Models/ExamSubject.php` - Update fillable to use max_marks
3. `app/Models/Mark.php` - Complete rewrite for student_marks table
4. `app/Models/Result.php` - Mark as non-functional (table doesn't exist)
5. `app/Models/Salary.php` - Mark as non-functional (table doesn't exist)
6. `app/Models/Grade.php` - Mark as non-functional (table doesn't exist)

**Controllers (4 files):**
7. `app/Http/Controllers/ExamController.php` - Update all queries
8. `app/Http/Controllers/MarkController.php` - Complete rewrite
9. `app/Http/Controllers/ResultController.php` - Comment out/disable
10. `app/Http/Controllers/SalaryController.php` - Comment out/disable

**Views (15+ files):**
11-15. `resources/views/exams/*.blade.php` (5 files)
16-19. `resources/views/marks/*.blade.php` (4 files)
20-22. `resources/views/results/*.blade.php` (3 files) - Comment out
23+. `resources/views/salaries/*.blade.php` - Comment out

**Routes:**
24. `routes/web.php` - Comment out non-functional routes

---

## 📈 IMPACT ANALYSIS

### Modules Currently Working: ✅
- Student Admissions
- Fee Management
- Library Management (Books, Issues, Fines)
- Staff Management (CRUD only, not salaries)
- Attendance
- Accounts (Income/Expense)
- User Management
- Permissions & Roles

### Modules Broken: ❌
- Examination Management (60% broken)
- Marks Entry (100% broken)
- Results (100% broken)
- Salary Management (100% broken)
- Grading System (100% broken)

### Estimated Downtime Risk:
- **Current:** 4 major modules completely non-functional
- **After Fix:** All modules functional but with reduced features (no results, no salaries until tables created)

---

## 🎓 CONCLUSION

The codebase has **significant schema mismatches** between Laravel models and the actual database migration. The primary issues are:

1. **Wrong column names** (class_id vs term_id in exams)
2. **Missing columns** (session, is_published, total_marks in exams)
3. **Wrong table names** (marks vs student_marks)
4. **Missing tables** (results, staff_salaries, grades)

These mismatches will cause **HTTP 500 errors** on approximately **30-40 routes** across 4 major modules.

**Recommended Action:** Follow the fix strategy above to align models with the actual migration schema. DO NOT modify the database schema - only fix the code to match what exists.

---

**End of Audit Report**
