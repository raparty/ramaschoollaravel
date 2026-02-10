# School ERP - Quick Answers to Your Questions

## 1. Which file is the main entry point of the application?

**Answer: `index.php`**

This is the **login page** - the very first page users see when they visit your website. It's like the front door of your application.

---

## 2. How does the login flow work (from first page to dashboard)?

Here's the **step-by-step journey**:

```
STEP 1: User visits website
   ↓
   Opens: index.php (Login Screen)
   User sees: Username and Password fields

STEP 2: User enters credentials and clicks "Sign In"
   ↓
   Sends data to: login_process.php
   
STEP 3: login_process.php checks credentials
   ↓
   IF WRONG → Back to index.php with error message
   IF CORRECT → Continue to next step

STEP 4: User selects school year/session
   ↓
   Opens: session.php
   User selects: "2023-2024" or "2024-2025" (school year)

STEP 5: After selecting session
   ↓
   Opens: dashboard.php (Main Control Panel)
   User sees: 10 colorful module cards to choose from

STEP 6: User clicks any module to start working
   ↓
   Can access: Admissions, Fees, Exams, Library, Transport, etc.
```

### Summary in Simple Terms:
1. **Login** at index.php
2. **Verify** identity via login_process.php
3. **Choose** school year at session.php
4. **Land** on dashboard.php
5. **Work** with any module

---

## 3. Which PHP files link or redirect to other PHP files?

Here are the **main connections**:

### Login Flow Redirects:
```
index.php 
   → login_process.php 
      → session.php 
         → dashboard.php
```

### Dashboard Links to Modules:
```
dashboard.php links to:
   ├── school_setting.php (School Settings)
   ├── admission.php (Admissions)
   ├── student_detail.php (Student Records)
   ├── fees_setting.php (Fees Management)
   ├── account_setting.php (Accounts)
   ├── exam_setting.php (Examinations)
   ├── transport_setting.php (Transport)
   ├── staff_setting.php (Staff Records)
   ├── library_setting.php (Library)
   └── entry_student_tc.php (Transfer Certificates)
```

### Common Operation Redirects (Happens in Every Module):
```
ADD OPERATION:
   manager_page.php → Click "Add" 
      → add_something.php (Form page)
         → Submit form 
            → Saves to database
               → Redirects back to manager_page.php with success message

EDIT OPERATION:
   manager_page.php → Click "Edit" on a row
      → edit_something.php?id=123 (Form with existing data)
         → Submit changes
            → Updates database
               → Redirects back to manager_page.php with update message

DELETE OPERATION:
   manager_page.php → Click "Delete" on a row
      → delete_something.php?id=123
         → Deletes from database
            → Redirects back to manager_page.php with deletion message
```

### Search Operations:
```
Any listing page → Click "Search"
   → searchby_name.php (Enter student name)
      → search_result.php (Shows matching students)
         → Click on a student
            → view_student_detail.php (Full profile)
```

### Authentication Protection:
Every page includes `includes/header.php` which checks:
```
If user NOT logged in 
   → Redirect to index.php (login page)
Otherwise
   → Show the page content
```

---

## 4. What are the main modules in this ERP?

Here are **ALL 10 MAIN MODULES** with their purposes:

### 📚 **1. ADMISSIONS** (`admission.php`)
**Purpose:** Enroll new students, manage admission records

**What you can do:**
- Add new students to the school
- Edit student admission information
- View list of all admitted students
- Search for students by name
- View individual student profiles

**Key Files:**
- `add_admission.php` - Add new student
- `edit_admission.php` - Edit student info
- `student_detail.php` - View all students
- `view_student_detail.php` - See one student's complete profile

---

### 💰 **2. FEES MANAGEMENT** (`fees_setting.php`)
**Purpose:** Handle all money collection - tuition, lab fees, sports fees, etc.

**What you can do:**
- Create fee packages (e.g., "Grade 1: ₹50,000/year")
- Collect fees from students
- Print receipts
- Track who hasn't paid yet (pending fees)
- Manage transport/bus fees separately
- Generate fee reports

**Key Files:**
- `fees_package.php` - Create fee structures
- `fees_manager.php` - Main fee collection screen
- `entry_fees_reciept.php` - Record payment and print receipt
- `student_pending_fees_detail.php` - See who owes money
- `student_transport_fees.php` - Bus fee management

---

### 📝 **3. EXAMINATIONS** (`exam_setting.php`)
**Purpose:** Manage tests, marks, report cards

**What you can do:**
- Create exam timetables
- Set maximum marks for each subject
- Enter student marks/grades
- Generate report cards (marksheets)
- Publish results
- Search student marks

**Key Files:**
- `exam_time_table_detail.php` - Exam schedule
- `exam_add_maximum_marks.php` - Set max marks (e.g., Math: 100, English: 100)
- `entry_exam_add_student_marks.php` - Enter marks
- `exam_final_marksheet.php` - Print report card
- `exam_result.php` - View results

---

### 📖 **4. LIBRARY** (`library_setting.php`)
**Purpose:** Manage books, book lending, returns, and fines

**What you can do:**
- Add books to library catalog
- Issue books to students
- Record book returns
- Calculate and collect late return fines
- Manage book categories (Fiction, Science, History, etc.)
- Track which student has which book

**Key Files:**
- `library_book_manager.php` - All books
- `library_add_book.php` - Add new book
- `library_add_student_books.php` - Lend book to student
- `library_entry_student_return_books.php` - Return books
- `library_fine_manager.php` - Late return fines

---

### 🚌 **5. TRANSPORT** (`transport_setting.php`)
**Purpose:** Manage school buses, routes, and transport fees

**What you can do:**
- Add bus routes (e.g., "Route 1: Main Street → School")
- Manage school buses/vehicles
- Assign students to buses
- Collect transport fees
- Print transport fee receipts
- Track which students use buses

**Key Files:**
- `transport_route_detail.php` - All bus routes
- `transport_vechile_detail.php` - All buses/vehicles
- `transport_student_detail.php` - Students using transport
- `transport_add_student.php` - Assign student to bus
- `entry_transport_fees_reciept.php` - Collect bus fees

---

### 👥 **6. STAFF RECORDS** (`staff_setting.php`)
**Purpose:** Manage all school employees (teachers, admin staff, etc.)

**What you can do:**
- Add new employees
- Edit employee information
- Organize staff by departments (Teaching, Admin, Support)
- Track qualifications (B.Ed, M.A., Ph.D.)
- Assign positions (Principal, HOD, Coordinator)
- View employee profiles

**Key Files:**
- `view_staff.php` - All employees
- `add_new_staff_detail.php` - Add new employee
- `view_staff_employee.php` - Employee profile
- `view_staff_department.php` - Departments
- `view_staff_qualification.php` - Education credentials

---

### 💵 **7. ACCOUNTS** (`account_setting.php`)
**Purpose:** Track school's money - what comes in and what goes out

**What you can do:**
- Record income (fee collections, donations, grants)
- Record expenses (salaries, electricity, supplies, maintenance)
- Categorize transactions
- Generate financial reports
- View daily income/expense summary

**Key Files:**
- `income_manager.php` - Record income
- `expense_manager.php` - Record expenses
- `add_income.php` - Add income entry
- `add_expense.php` - Add expense entry
- `account_report.php` - Financial reports
- `daily_report.php` - Daily summary

---

### 🎓 **8. STUDENT DETAILS** (`student_detail.php`)
**Purpose:** Central hub to view and search all student information

**What you can do:**
- View complete list of all students
- Search for specific students
- See student profiles
- Check fee payment history
- View transport fee records
- Track library fines

**Key Files:**
- `student_detail.php` - Main student list
- `view_student_detail.php` - Individual student profile
- `searchby_name.php` - Search students
- `student_fees_reports.php` - Fee history
- `student_pending_fees_detail.php` - Outstanding fees

---

### 📄 **9. STUDENT TC - Transfer Certificates** (`entry_student_tc.php`)
**Purpose:** Issue transfer certificates when students leave school

**What you can do:**
- Generate transfer certificates
- View all issued TCs
- Search students for TC
- Print transfer certificates
- Maintain TC records

**Key Files:**
- `entry_student_tc.php` - Create new TC
- `student_tc.php` - View all TCs
- `student_tc_show.php` - Display TC
- `student_tc_search_by_name.php` - Find student

---

### ⚙️ **10. SCHOOL SETTINGS** (`school_setting.php`)
**Purpose:** Basic school configuration and setup

**What you can do:**
- Set school name, address, logo
- Create classes (Grade 1, Grade 2, etc.)
- Create sections (Section A, B, C)
- Add streams (Science, Commerce, Arts)
- Add subjects (Math, English, Science)
- Allocate subjects to classes

**Key Files:**
- `school_detail.php` - School basic info
- `class.php` - Manage classes
- `section.php` - Manage sections
- `stream.php` - Manage streams
- `subject.php` - Manage subjects
- `allocate_subject.php` - Assign subjects to classes

---

## Summary Table of Modules

| # | Module Name | Entry File | Main Purpose |
|---|-------------|------------|--------------|
| 1 | Admissions | `admission.php` | Enroll new students |
| 2 | Fees Management | `fees_setting.php` | Collect fees, print receipts |
| 3 | Examinations | `exam_setting.php` | Manage exams, marks, report cards |
| 4 | Library | `library_setting.php` | Books, lending, fines |
| 5 | Transport | `transport_setting.php` | Buses, routes, transport fees |
| 6 | Staff Records | `staff_setting.php` | Employee management |
| 7 | Accounts | `account_setting.php` | Income & expense tracking |
| 8 | Student Details | `student_detail.php` | View/search all students |
| 9 | Student TC | `entry_student_tc.php` | Transfer certificates |
| 10 | School Settings | `school_setting.php` | Basic school setup |

---

## Simple Navigation Diagram (All in One)

```
START HERE
    ↓
index.php (Login) 
    ↓
login_process.php (Verify)
    ↓
session.php (Choose Year)
    ↓
dashboard.php ────────────┬─────────────────────────────┐
    ↓                     ↓                             ↓
    │                     │                             │
    ├→ admission.php      ├→ fees_setting.php          ├→ exam_setting.php
    │  (New Students)     │  (Collect Fees)            │  (Marks, Reports)
    │                     │                             │
    ├→ student_detail.php ├→ library_setting.php       ├→ transport_setting.php
    │  (View Students)    │  (Books)                   │  (Buses)
    │                     │                             │
    ├→ staff_setting.php  ├→ account_setting.php       ├→ school_setting.php
    │  (Employees)        │  (Money)                   │  (Setup)
    │                     │                             │
    └→ entry_student_tc.php ← Transfer Certificates
```

---

## How Everything Connects (Real Example)

Let's say a **new student named Rajesh joins your school**. Here's what happens:

1. **Admin logs in** → `index.php` → `login_process.php` → `session.php` → `dashboard.php`

2. **Admin adds Rajesh** → Click "Admission" card → `admission.php` → `add_admission.php` → Fill form → Save → Rajesh is now in the system!

3. **Admin assigns fees to Rajesh** → Click "Fees Manager" → `fees_setting.php` → `fees_manager.php` → Search "Rajesh" → `add_student_fees.php` → Assign "Grade 1 Fee Package (₹50,000)" → Save

4. **Rajesh's parents pay first term fees** → `entry_fees_reciept.php` → Enter "₹12,500 paid" → Print receipt → `fees_reciept.php`

5. **Admin assigns Rajesh to Bus Route 3** → Click "Transport" → `transport_setting.php` → `transport_student_detail.php` → `transport_add_student.php` → Select "Rajesh" → Assign "Route 3" → Save

6. **Admin issues library book to Rajesh** → Click "Library" → `library_setting.php` → `library_student_books_manager.php` → `library_add_student_books.php` → Select "Rajesh" → Issue "Harry Potter Book 1" → Save

7. **Teacher enters exam marks for Rajesh** → Click "Examinations" → `exam_setting.php` → `entry_exam_add_student_marks.php` → Select "Rajesh" → Enter marks: Math=85, English=78 → Save

8. **Admin prints Rajesh's report card** → `exam_final_marksheet.php` → Select "Rajesh" → Print

All of Rajesh's information is now connected across multiple modules!

---

## Need More Help?

- **Full detailed guide:** See `ERP_APPLICATION_GUIDE.md`
- **Visual flow diagrams:** See `NAVIGATION_FLOW_DIAGRAM.txt`
- **Database structure:** See `db/school_erp_schema_audit.sql`

---

**That's it!** You now understand how the entire School ERP system works. 🎉
