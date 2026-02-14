# Database Tables Visual Reference

Quick visual reference for the 5 new database tables.

---

## 📋 1. EXAMS Table

```
┌─────────────────────────────────────────────────────────────┐
│ exams                                                        │
├──────────────┬──────────────┬───────────┬──────────────────┤
│ Column       │ Type         │ Null      │ Description       │
├──────────────┼──────────────┼───────────┼──────────────────┤
│ id           │ INT          │ NOT NULL  │ Primary Key       │
│ name         │ VARCHAR(255) │ NOT NULL  │ Midterm, Final... │
│ class_id     │ INT          │ NOT NULL  │ FK → classes      │
│ session      │ VARCHAR(50)  │ NOT NULL  │ 2023-2024        │
│ start_date   │ DATE         │ NOT NULL  │ Exam start       │
│ end_date     │ DATE         │ NOT NULL  │ Exam end         │
│ total_marks  │ INT          │ NOT NULL  │ Default: 100     │
│ pass_marks   │ INT          │ NOT NULL  │ Default: 40      │
│ is_published │ TINYINT(1)   │ NOT NULL  │ 0=No, 1=Yes      │
│ description  │ TEXT         │ NULL      │ Instructions     │
│ created_at   │ TIMESTAMP    │ NULL      │ Auto             │
│ updated_at   │ TIMESTAMP    │ NULL      │ Auto             │
│ deleted_at   │ TIMESTAMP    │ NULL      │ Soft delete      │
└──────────────┴──────────────┴───────────┴──────────────────┘
```

**Purpose:** Store examination schedules (Midterm, Final, Unit Tests)  
**Relationships:** Belongs to Class, Has many ExamSubjects, Has many Results

---

## 📝 2. EXAM_SUBJECTS Table

```
┌────────────────────────────────────────────────────────────┐
│ exam_subjects                                               │
├─────────────────┬─────────────┬───────────┬────────────────┤
│ Column          │ Type        │ Null      │ Description     │
├─────────────────┼─────────────┼───────────┼────────────────┤
│ id              │ INT         │ NOT NULL  │ Primary Key     │
│ exam_id         │ INT         │ NOT NULL  │ FK → exams      │
│ subject_id      │ INT         │ NOT NULL  │ FK → subjects   │
│ theory_marks    │ INT         │ NOT NULL  │ Default: 70     │
│ practical_marks │ INT         │ NOT NULL  │ Default: 30     │
│ pass_marks      │ INT         │ NOT NULL  │ Default: 40     │
│ exam_date       │ DATE        │ NULL      │ Specific date   │
│ exam_time       │ TIME        │ NULL      │ Start time      │
│ duration_min    │ INT         │ NULL      │ Default: 180    │
│ created_at      │ TIMESTAMP   │ NULL      │ Auto            │
│ updated_at      │ TIMESTAMP   │ NULL      │ Auto            │
└─────────────────┴─────────────┴───────────┴────────────────┘
```

**Purpose:** Link subjects to exams with marks configuration  
**Relationships:** Belongs to Exam, Belongs to Subject, Has many Marks  
**Unique Constraint:** (exam_id, subject_id) - One subject per exam

---

## 🎓 3. RESULTS Table

```
┌────────────────────────────────────────────────────────────────┐
│ results                                                         │
├────────────────────┬──────────────┬───────────┬────────────────┤
│ Column             │ Type         │ Null      │ Description     │
├────────────────────┼──────────────┼───────────┼────────────────┤
│ id                 │ INT          │ NOT NULL  │ Primary Key     │
│ student_id         │ INT          │ NOT NULL  │ FK → admissions │
│ exam_id            │ INT          │ NOT NULL  │ FK → exams      │
│ total_marks_obtnd  │ DECIMAL(10,2)│ NOT NULL  │ Total scored    │
│ total_max_marks    │ INT          │ NOT NULL  │ Max possible    │
│ percentage         │ DECIMAL(5,2) │ NOT NULL  │ Calculated %    │
│ grade              │ VARCHAR(10)  │ NULL      │ A+, A, B, etc.  │
│ rank               │ INT          │ NULL      │ Class rank      │
│ is_passed          │ TINYINT(1)   │ NOT NULL  │ 0=Fail, 1=Pass  │
│ is_published       │ TINYINT(1)   │ NOT NULL  │ 0=No, 1=Yes     │
│ remarks            │ TEXT         │ NULL      │ Comments        │
│ created_at         │ TIMESTAMP    │ NULL      │ Auto            │
│ updated_at         │ TIMESTAMP    │ NULL      │ Auto            │
└────────────────────┴──────────────┴───────────┴────────────────┘
```

**Purpose:** Store compiled student results for each exam  
**Relationships:** Belongs to Student (Admission), Belongs to Exam  
**Unique Constraint:** (student_id, exam_id) - One result per student per exam

---

## 💰 4. STAFF_SALARIES Table

```
┌──────────────────────────────────────────────────────────────┐
│ staff_salaries                                                │
├───────────────┬──────────────┬───────────┬───────────────────┤
│ Column        │ Type         │ Null      │ Description        │
├───────────────┼──────────────┼───────────┼───────────────────┤
│ id            │ INT          │ NOT NULL  │ Primary Key        │
│ staff_id      │ INT          │ NOT NULL  │ FK → staff_employee│
│ month         │ TINYINT      │ NOT NULL  │ 1-12               │
│ year          │ YEAR         │ NOT NULL  │ 2024               │
│ basic_salary  │ DECIMAL(10,2)│ NOT NULL  │ Base amount        │
│ allowances    │ DECIMAL(10,2)│ NOT NULL  │ HRA, DA, etc.      │
│ deductions    │ DECIMAL(10,2)│ NOT NULL  │ PF, Tax, etc.      │
│ net_salary    │ DECIMAL(10,2)│ NOT NULL  │ Final amount       │
│ status        │ ENUM         │ NOT NULL  │ pending/paid       │
│ payment_date  │ DATE         │ NULL      │ When paid          │
│ payment_method│ VARCHAR(50)  │ NULL      │ Cash/Bank/Cheque   │
│ notes         │ TEXT         │ NULL      │ Additional notes   │
│ created_at    │ TIMESTAMP    │ NULL      │ Auto               │
│ updated_at    │ TIMESTAMP    │ NULL      │ Auto               │
└───────────────┴──────────────┴───────────┴───────────────────┘
```

**Purpose:** Track monthly staff salary payments  
**Relationships:** Belongs to Staff  
**Unique Constraint:** (staff_id, month, year) - One salary per staff per month

---

## 🏆 5. GRADES Table (with Sample Data)

```
┌─────────────────────────────────────────────────────────────┐
│ grades                                                       │
├───────────────┬──────────────┬──────────┬──────────────────┤
│ Column        │ Type         │ Null     │ Description       │
├───────────────┼──────────────┼──────────┼──────────────────┤
│ id            │ INT          │ NOT NULL │ Primary Key       │
│ grade         │ VARCHAR(10)  │ NOT NULL │ A+, A, B, etc.    │
│ min_percentage│ DECIMAL(5,2) │ NOT NULL │ Minimum %         │
│ max_percentage│ DECIMAL(5,2) │ NOT NULL │ Maximum %         │
│ points        │ DECIMAL(3,2) │ NOT NULL │ GPA points        │
│ description   │ TEXT         │ NULL     │ Outstanding, etc. │
│ created_at    │ TIMESTAMP    │ NULL     │ Auto              │
│ updated_at    │ TIMESTAMP    │ NULL     │ Auto              │
└───────────────┴──────────────┴──────────┴──────────────────┘

Sample Data (Pre-populated):
┌───────┬──────┬──────┬────────┬───────────────┐
│ Grade │ Min% │ Max% │ Points │ Description   │
├───────┼──────┼──────┼────────┼───────────────┤
│ A+    │ 90   │ 100  │ 4.00   │ Outstanding   │
│ A     │ 80   │ 89.99│ 3.70   │ Excellent     │
│ B+    │ 70   │ 79.99│ 3.30   │ Very Good     │
│ B     │ 60   │ 69.99│ 3.00   │ Good          │
│ C+    │ 50   │ 59.99│ 2.70   │ Above Average │
│ C     │ 40   │ 49.99│ 2.00   │ Average       │
│ D     │ 33   │ 39.99│ 1.00   │ Pass          │
│ F     │ 0    │ 32.99│ 0.00   │ Fail          │
└───────┴──────┴──────┴────────┴───────────────┘
```

**Purpose:** Define grading system for automatic grade assignment  
**Unique Constraint:** grade (each grade name is unique)

---

## 🔗 Relationship Diagram

```
┌─────────┐         ┌──────────────┐         ┌─────────┐
│ classes │◄────────│    exams     │────────►│ results │
└─────────┘         └──────┬───────┘         └────┬────┘
                           │                      │
                           │                      │
                    ┌──────▼──────┐         ┌─────▼───────┐
                    │exam_subjects│         │ admissions  │
                    └──────┬──────┘         │  (students) │
                           │                └─────────────┘
                    ┌──────▼──────┐
                    │  subjects   │
                    └─────────────┘

┌──────────────┐         ┌────────────────┐
│staff_employee│◄────────│staff_salaries  │
└──────────────┘         └────────────────┘

┌────────┐
│ grades │  (Standalone configuration table)
└────────┘
```

---

## 📊 Storage Estimates

Approximate storage per record:
- **exams**: ~200 bytes per exam
- **exam_subjects**: ~150 bytes per subject-exam link
- **results**: ~250 bytes per student result
- **staff_salaries**: ~200 bytes per salary record
- **grades**: ~100 bytes per grade (8 records pre-populated)

For a school with:
- 500 students
- 50 staff
- 10 classes
- 10 subjects per class
- 4 exams per year

**Annual storage**: ~1-2 MB (very light!)

---

## 🚀 Quick Actions After Installation

```php
// Create your first exam
$exam = Exam::create([
    'name' => 'Mid-Term 2024',
    'class_id' => 1,
    'session' => '2023-2024',
    'start_date' => '2024-03-01',
    'end_date' => '2024-03-15',
    'total_marks' => 500,
    'pass_marks' => 200,
]);

// Add subjects to exam
ExamSubject::create([
    'exam_id' => $exam->id,
    'subject_id' => 1, // Math
    'theory_marks' => 70,
    'practical_marks' => 30,
]);

// Get grade for percentage
$grade = Grade::forPercentage(85.5); // Returns "A"

// Record salary payment
Salary::create([
    'staff_id' => 1,
    'month' => 2,
    'year' => 2024,
    'basic_salary' => 50000,
    'allowances' => 10000,
    'deductions' => 5000,
    'net_salary' => 55000,
    'status' => 'paid',
]);
```

---

**Ready to use!** All tables are indexed and optimized for performance.
