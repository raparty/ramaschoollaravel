# School ERP System - Database Schema Documentation

## 📊 Database Overview

The School ERP System uses a MySQL database with a well-normalized structure designed to handle all aspects of school management efficiently.

### Database Information
- **Database Name**: school_erp (or school_erp_modern for new deployments)
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **Engine**: InnoDB (for transactions and foreign key support)
- **Total Tables**: 28+ tables

---

## 🗂️ Database Architecture

### Entity Relationship Overview

```
┌────────────┐
│  Schools   │
└──────┬─────┘
       │
       ├─────────────┬──────────────┬───────────────┬────────────────┐
       │             │              │               │                │
    ┌──▼───┐    ┌───▼────┐    ┌───▼────┐     ┌───▼────┐      ┌───▼────┐
    │Users │    │Classes │    │ Staff  │     │Students│      │ Fees   │
    └──────┘    └───┬────┘    └────────┘     └────┬───┘      └───┬────┘
                    │                              │              │
              ┌─────┴─────┐                   ┌───▼────┐    ┌───▼────┐
              │           │                   │Guardian│    │ Fee    │
          ┌───▼─┐    ┌───▼───┐              └────────┘    │Receipts│
          │Sect.│    │Stream │                             └────────┘
          └─────┘    └───┬───┘
                         │
                    ┌────▼────┐
                    │Subjects │
                    └─────────┘
```

---

## 📋 Core Tables

### 1. schools
**Purpose**: Store school basic information and configuration

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique school identifier |
| name | VARCHAR(255) | NOT NULL | School name |
| code | VARCHAR(50) | UNIQUE | School code/registration number |
| address_line1 | VARCHAR(255) | | Street address line 1 |
| address_line2 | VARCHAR(255) | | Street address line 2 |
| city | VARCHAR(100) | | City name |
| state | VARCHAR(100) | | State/Province |
| postal_code | VARCHAR(20) | | ZIP/Postal code |
| country | VARCHAR(100) | | Country |
| phone | VARCHAR(50) | | Contact phone number |
| email | VARCHAR(255) | | Contact email address |
| website | VARCHAR(255) | | School website URL |
| logo_path | VARCHAR(255) | | Path to school logo |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Last update timestamp |

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `code`

---

### 2. users
**Purpose**: System authentication and user management

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | User ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Reference to schools table |
| username | VARCHAR(100) | UNIQUE, NOT NULL | Login username |
| email | VARCHAR(255) | | User email address |
| password_hash | VARCHAR(255) | NOT NULL | Encrypted password |
| role | VARCHAR(50) | NOT NULL | User role (admin, staff, etc.) |
| status | VARCHAR(20) | DEFAULT 'active' | Account status |
| last_login_at | TIMESTAMP | | Last login time |
| created_at | TIMESTAMP | | Registration date |
| updated_at | TIMESTAMP | | Last profile update |

**Foreign Keys**:
- `school_id` → `schools(id)`

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `username`
- INDEX on `school_id`

---

## 👨‍🎓 Student Management Tables

### 3. admissions
**Purpose**: Student registration and admission records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Admission ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| reg_no | VARCHAR(50) | UNIQUE, NOT NULL | Student registration number |
| student_name | VARCHAR(255) | NOT NULL | Full name |
| student_pic | VARCHAR(255) | | Photo file path |
| dob | DATE | NOT NULL | Date of birth |
| gender | ENUM('Male','Female','Other') | NOT NULL | Gender |
| blood_group | VARCHAR(10) | | Blood group (A+, B+, etc.) |
| class_id | INT UNSIGNED | FOREIGN KEY | Current class |
| section_id | INT UNSIGNED | FOREIGN KEY | Current section |
| admission_date | DATE | NOT NULL | Date of admission |
| aadhaar_no | VARCHAR(20) | | Aadhaar number (India) |
| aadhaar_doc_path | VARCHAR(255) | | Aadhaar document path |
| guardian_name | VARCHAR(255) | NOT NULL | Parent/Guardian name |
| guardian_phone | VARCHAR(20) | NOT NULL | Contact number |
| guardian_email | VARCHAR(255) | | Email address |
| guardian_address | TEXT | | Residential address |
| past_school_info | TEXT | | Previous school details |
| status | VARCHAR(20) | DEFAULT 'active' | Student status |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `class_id` → `classes(id)`
- `section_id` → `sections(id)`

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `reg_no`
- INDEX on `school_id`, `class_id`, `section_id`
- INDEX on `student_name` (for search)

---

### 4. student_tc
**Purpose**: Transfer Certificate records for students leaving school

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | TC ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| tc_number | VARCHAR(50) | UNIQUE | TC number |
| date_of_leaving | DATE | NOT NULL | Leaving date |
| class_at_leaving | VARCHAR(50) | | Class when leaving |
| reason_for_leaving | VARCHAR(255) | | Reason |
| conduct | TEXT | | Student conduct remarks |
| remarks | TEXT | | Additional remarks |
| issued_by | INT UNSIGNED | FOREIGN KEY | User who issued TC |
| issued_date | DATE | | TC issue date |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `issued_by` → `users(id)`

---

## 🎓 Academic Management Tables

### 5. academic_sessions
**Purpose**: Manage academic years/sessions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Session ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(50) | NOT NULL | Session name (e.g., "2023-24") |
| start_date | DATE | NOT NULL | Session start date |
| end_date | DATE | NOT NULL | Session end date |
| is_active | TINYINT(1) | DEFAULT 0 | Current active session |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`

---

### 6. classes
**Purpose**: Academic class/grade definitions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Class ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(100) | NOT NULL | Class name (e.g., "Class 10") |
| has_streams | TINYINT(1) | DEFAULT 0 | Whether class has streams |
| display_order | INT | | Display sequence |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`

**Indexes**:
- UNIQUE KEY on (`school_id`, `name`)

---

### 7. sections
**Purpose**: Class sections (divisions)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Section ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class reference |
| name | VARCHAR(100) | NOT NULL | Section name (A, B, C, etc.) |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `class_id` → `classes(id)`

**Indexes**:
- UNIQUE KEY on (`class_id`, `name`)

---

### 8. streams
**Purpose**: Academic streams (Science, Commerce, Arts)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Stream ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(100) | NOT NULL | Stream name |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`

---

### 9. subjects
**Purpose**: Subject master list

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Subject ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Subject name |
| code | VARCHAR(50) | | Subject code |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`

---

### 10. class_stream_allocations
**Purpose**: Map streams to classes

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Allocation ID |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class reference |
| stream_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Stream reference |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `class_id` → `classes(id)`
- `stream_id` → `streams(id)`

---

### 11. class_subject_allocations
**Purpose**: Map subjects to class/stream combinations

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Allocation ID |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class reference |
| stream_id | INT UNSIGNED | FOREIGN KEY | Optional stream |
| subject_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Subject reference |
| is_compulsory | TINYINT(1) | DEFAULT 1 | Mandatory subject |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `class_id` → `classes(id)`
- `stream_id` → `streams(id)`
- `subject_id` → `subjects(id)`

---

## 👔 Staff Management Tables

### 12. staff_departments
**Purpose**: Staff department categorization

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Department ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Department name |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 13. staff_categories
**Purpose**: Staff category types (Teaching/Non-teaching)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Category ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Category name |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 14. staff_positions
**Purpose**: Job position definitions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Position ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Position title |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 15. staff
**Purpose**: Staff/employee records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Staff ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| employee_id | VARCHAR(50) | UNIQUE, NOT NULL | Employee number |
| name | VARCHAR(255) | NOT NULL | Full name |
| email | VARCHAR(255) | | Email address |
| phone | VARCHAR(20) | | Phone number |
| dob | DATE | | Date of birth |
| gender | ENUM('Male','Female','Other') | | Gender |
| category_id | INT UNSIGNED | FOREIGN KEY | Staff category |
| department_id | INT UNSIGNED | FOREIGN KEY | Department |
| position_id | INT UNSIGNED | FOREIGN KEY | Position |
| qualification | VARCHAR(255) | | Educational qualification |
| joining_date | DATE | | Date of joining |
| salary | DECIMAL(10,2) | | Monthly salary |
| photo_path | VARCHAR(255) | | Photo file path |
| address | TEXT | | Residential address |
| status | VARCHAR(20) | DEFAULT 'active' | Employment status |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `category_id` → `staff_categories(id)`
- `department_id` → `staff_departments(id)`
- `position_id` → `staff_positions(id)`

---

## 💰 Fees Management Tables

### 16. fee_packages
**Purpose**: Fee structure packages

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Package ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Package name |
| description | TEXT | | Package description |
| total_amount | DECIMAL(10,2) | NOT NULL | Total annual fee |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 17. fee_components
**Purpose**: Individual fee items (Tuition, Library, etc.)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Component ID |
| package_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Fee package |
| name | VARCHAR(150) | NOT NULL | Component name |
| amount | DECIMAL(10,2) | NOT NULL | Amount |
| is_compulsory | TINYINT(1) | DEFAULT 1 | Mandatory component |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `package_id` → `fee_packages(id)`

---

### 18. student_fees
**Purpose**: Fee assignments to students

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Assignment ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| package_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Fee package |
| session_id | INT UNSIGNED | FOREIGN KEY | Academic session |
| total_fee | DECIMAL(10,2) | NOT NULL | Total assigned fee |
| paid_amount | DECIMAL(10,2) | DEFAULT 0 | Amount paid so far |
| pending_amount | DECIMAL(10,2) | | Remaining balance |
| status | VARCHAR(20) | DEFAULT 'pending' | Payment status |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `package_id` → `fee_packages(id)`
- `session_id` → `academic_sessions(id)`

---

### 19. fee_receipts
**Purpose**: Fee payment transactions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Receipt ID |
| receipt_no | VARCHAR(50) | UNIQUE, NOT NULL | Receipt number |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| student_fee_id | INT UNSIGNED | FOREIGN KEY | Student fee assignment |
| term_id | INT UNSIGNED | FOREIGN KEY | Academic term |
| amount | DECIMAL(10,2) | NOT NULL | Paid amount |
| payment_mode | VARCHAR(50) | NOT NULL | Cash/Cheque/Online |
| payment_ref | VARCHAR(100) | | Cheque/Transaction ref |
| remarks | TEXT | | Additional notes |
| collected_by | INT UNSIGNED | FOREIGN KEY | User who collected |
| payment_date | DATE | NOT NULL | Payment date |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `student_fee_id` → `student_fees(id)`
- `term_id` → `exam_terms(id)`
- `collected_by` → `users(id)`

---

### 20. student_fines
**Purpose**: Late payment fines

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Fine ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| amount | DECIMAL(10,2) | NOT NULL | Fine amount |
| reason | VARCHAR(255) | | Fine reason |
| fine_date | DATE | NOT NULL | Date of fine |
| paid | TINYINT(1) | DEFAULT 0 | Payment status |
| paid_date | DATE | | Payment date |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `student_id` → `admissions(id)`

---

## 📚 Library Management Tables

### 21. library_book_categories
**Purpose**: Book categorization

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Category ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Category name |
| created_at | TIMESTAMP | | Record creation |

---

### 22. library_books
**Purpose**: Book inventory

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Book ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| category_id | INT UNSIGNED | FOREIGN KEY | Book category |
| title | VARCHAR(255) | NOT NULL | Book title |
| author | VARCHAR(255) | | Author name |
| isbn | VARCHAR(50) | | ISBN number |
| publisher | VARCHAR(255) | | Publisher name |
| edition | VARCHAR(50) | | Edition |
| total_copies | INT | NOT NULL | Total copies |
| available_copies | INT | NOT NULL | Available copies |
| purchase_date | DATE | | Purchase date |
| price | DECIMAL(10,2) | | Book price |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `category_id` → `library_book_categories(id)`

---

### 23. library_book_issues
**Purpose**: Book issue/return transactions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Issue ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| book_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Book reference |
| issue_date | DATE | NOT NULL | Issue date |
| expected_return_date | DATE | NOT NULL | Expected return |
| actual_return_date | DATE | | Actual return |
| status | VARCHAR(20) | DEFAULT 'issued' | issued/returned |
| issued_by | INT UNSIGNED | FOREIGN KEY | User who issued |
| returned_to | INT UNSIGNED | FOREIGN KEY | User who received |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `book_id` → `library_books(id)`
- `issued_by` → `users(id)`
- `returned_to` → `users(id)`

---

### 24. library_fines
**Purpose**: Library fine records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Fine ID |
| issue_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Book issue reference |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| amount | DECIMAL(10,2) | NOT NULL | Fine amount |
| days_overdue | INT | | Days overdue |
| paid | TINYINT(1) | DEFAULT 0 | Payment status |
| paid_date | DATE | | Payment date |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `issue_id` → `library_book_issues(id)`
- `student_id` → `admissions(id)`

---

## 🚌 Transport Management Tables

### 25. transport_routes
**Purpose**: Bus route definitions

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Route ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Route name/number |
| pickup_points | TEXT | | List of pickup points |
| distance_km | DECIMAL(5,2) | | Total distance |
| monthly_fee | DECIMAL(10,2) | | Monthly transport fee |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 26. transport_vehicles
**Purpose**: Vehicle information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Vehicle ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| registration_no | VARCHAR(50) | UNIQUE, NOT NULL | Vehicle number |
| vehicle_type | VARCHAR(50) | | Bus/Van/Car |
| capacity | INT | | Seating capacity |
| driver_name | VARCHAR(255) | | Driver name |
| driver_phone | VARCHAR(20) | | Driver contact |
| conductor_name | VARCHAR(255) | | Conductor name |
| conductor_phone | VARCHAR(20) | | Conductor contact |
| status | VARCHAR(20) | DEFAULT 'active' | Vehicle status |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 27. student_transport
**Purpose**: Student transport allocations

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Allocation ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| route_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Route reference |
| vehicle_id | INT UNSIGNED | FOREIGN KEY | Vehicle reference |
| pickup_point | VARCHAR(255) | | Specific pickup location |
| monthly_fee | DECIMAL(10,2) | | Transport fee |
| start_date | DATE | | Service start date |
| end_date | DATE | | Service end date |
| status | VARCHAR(20) | DEFAULT 'active' | Service status |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `route_id` → `transport_routes(id)`
- `vehicle_id` → `transport_vehicles(id)`

---

## 📝 Examination Tables

### 28. exam_terms
**Purpose**: Examination periods/terms

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Term ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| session_id | INT UNSIGNED | FOREIGN KEY | Academic session |
| name | VARCHAR(100) | NOT NULL | Term name |
| start_date | DATE | | Term start |
| end_date | DATE | | Term end |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

---

### 29. exam_schedules
**Purpose**: Examination time table

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Schedule ID |
| term_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Exam term |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class |
| subject_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Subject |
| exam_date | DATE | NOT NULL | Examination date |
| start_time | TIME | | Start time |
| end_time | TIME | | End time |
| room_no | VARCHAR(50) | | Examination hall |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `term_id` → `exam_terms(id)`
- `class_id` → `classes(id)`
- `subject_id` → `subjects(id)`

---

### 30. exam_maximum_marks
**Purpose**: Maximum marks configuration

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Config ID |
| term_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Exam term |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class |
| subject_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Subject |
| max_marks | INT | NOT NULL | Total maximum marks |
| theory_marks | INT | | Theory portion |
| practical_marks | INT | | Practical portion |
| internal_marks | INT | | Internal assessment |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `term_id` → `exam_terms(id)`
- `class_id` → `classes(id)`
- `subject_id` → `subjects(id)`

---

### 31. exam_marks
**Purpose**: Student examination marks

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Marks ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| term_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Exam term |
| subject_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Subject |
| marks_obtained | DECIMAL(5,2) | | Marks scored |
| theory_marks | DECIMAL(5,2) | | Theory marks |
| practical_marks | DECIMAL(5,2) | | Practical marks |
| internal_marks | DECIMAL(5,2) | | Internal marks |
| grade | VARCHAR(10) | | Calculated grade |
| remarks | TEXT | | Teacher remarks |
| entered_by | INT UNSIGNED | FOREIGN KEY | User who entered |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `term_id` → `exam_terms(id)`
- `subject_id` → `subjects(id)`
- `entered_by` → `users(id)`

---

## 💼 Accounts Tables

### 32. account_categories
**Purpose**: Income/Expense categories

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Category ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| name | VARCHAR(150) | NOT NULL | Category name |
| type | ENUM('income','expense') | NOT NULL | Transaction type |
| parent_id | INT UNSIGNED | FOREIGN KEY | Parent category (for sub-categories) |
| created_at | TIMESTAMP | | Record creation |
| updated_at | TIMESTAMP | | Last update |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `parent_id` → `account_categories(id)` (self-reference)

---

### 33. income_transactions
**Purpose**: Income records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Transaction ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| category_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Income category |
| amount | DECIMAL(10,2) | NOT NULL | Income amount |
| transaction_date | DATE | NOT NULL | Transaction date |
| payment_mode | VARCHAR(50) | | Cash/Cheque/Online |
| reference_no | VARCHAR(100) | | Cheque/Transaction ref |
| description | TEXT | | Transaction description |
| recorded_by | INT UNSIGNED | FOREIGN KEY | User who recorded |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `category_id` → `account_categories(id)`
- `recorded_by` → `users(id)`

---

### 34. expense_transactions
**Purpose**: Expense records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Transaction ID |
| school_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | School reference |
| category_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Expense category |
| amount | DECIMAL(10,2) | NOT NULL | Expense amount |
| transaction_date | DATE | NOT NULL | Transaction date |
| payment_mode | VARCHAR(50) | | Cash/Cheque/Online |
| reference_no | VARCHAR(100) | | Cheque/Transaction ref |
| description | TEXT | | Transaction description |
| recorded_by | INT UNSIGNED | FOREIGN KEY | User who recorded |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `school_id` → `schools(id)`
- `category_id` → `account_categories(id)`
- `recorded_by` → `users(id)`

---

## ✅ Attendance Table

### 35. attendance
**Purpose**: Daily student attendance records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Attendance ID |
| student_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Student reference |
| class_id | INT UNSIGNED | FOREIGN KEY, NOT NULL | Class |
| section_id | INT UNSIGNED | FOREIGN KEY | Section |
| attendance_date | DATE | NOT NULL | Date |
| status | ENUM('present','absent','late','leave') | NOT NULL | Attendance status |
| remarks | TEXT | | Additional notes |
| marked_by | INT UNSIGNED | FOREIGN KEY | User who marked |
| created_at | TIMESTAMP | | Record creation |

**Foreign Keys**:
- `student_id` → `admissions(id)`
- `class_id` → `classes(id)`
- `section_id` → `sections(id)`
- `marked_by` → `users(id)`

**Indexes**:
- UNIQUE KEY on (`student_id`, `attendance_date`)
- INDEX on `attendance_date`

---

## 🔗 Common Relationships

### One-to-Many Relationships
- schools → users (1:N)
- schools → classes (1:N)
- classes → sections (1:N)
- classes → streams (1:N)
- fee_packages → fee_components (1:N)
- students → fee_receipts (1:N)
- students → exam_marks (1:N)
- students → attendance (1:N)

### Many-to-Many Relationships (via junction tables)
- classes ↔ streams (via class_stream_allocations)
- classes ↔ subjects (via class_subject_allocations)

---

## 📊 Indexes & Performance

### Primary Indexes
- All tables have AUTO_INCREMENT PRIMARY KEY on `id`

### Secondary Indexes
- Student search: INDEX on `student_name`, `reg_no`
- Fee lookups: INDEX on `student_id`, `receipt_no`
- Attendance: INDEX on `attendance_date`, `student_id`
- Examinations: INDEX on `term_id`, `class_id`, `subject_id`
- Accounts: INDEX on `transaction_date`, `category_id`

### Foreign Key Indexes
- All foreign key columns are automatically indexed for join performance

---

## 🔒 Data Integrity

### Referential Integrity
- ON DELETE: Typically RESTRICT to prevent accidental data loss
- ON UPDATE: CASCADE for ID changes
- Foreign keys ensure data consistency across related tables

### Constraints
- NOT NULL on critical fields (names, dates, amounts)
- UNIQUE constraints on business keys (reg_no, receipt_no, employee_id)
- CHECK constraints on enumerations (gender, status)
- DEFAULT values for status fields

---

## 📈 Database Statistics

| Metric | Value |
|--------|-------|
| Total Tables | 35+ |
| Total Columns | 400+ |
| Foreign Keys | 60+ |
| Indexes | 100+ |
| Maximum Table Size | admissions (potential millions of rows) |
| Average Row Size | Varies (500 bytes - 2KB) |
| Storage Engine | InnoDB |
| Character Set | UTF-8 (utf8mb4) |

---

## 🛠️ Maintenance

### Backup Strategy
```sql
-- Full database backup
mysqldump -u username -p school_erp > backup_$(date +%Y%m%d).sql

-- Tables to backup daily: fee_receipts, attendance
-- Tables to backup weekly: All transaction tables
-- Tables to backup monthly: Full database
```

### Optimization
```sql
-- Analyze tables for query optimization
ANALYZE TABLE admissions, fee_receipts, exam_marks;

-- Optimize tables (defragment)
OPTIMIZE TABLE admissions, attendance;

-- Check table integrity
CHECK TABLE admissions EXTENDED;
```

---

## 📝 Migration Notes

### From Legacy to Modern Schema
1. Map old table structures to new normalized tables
2. Convert legacy data types to modern equivalents
3. Establish foreign key relationships
4. Migrate historical data with data validation
5. Update application code to use new schema

### Version History
- v1.0: Initial legacy schema
- v2.0: Modernized normalized schema (current)

---

**Document Version**: 1.0  
**Database Version**: 2.0 (Modern Schema)  
**Last Updated**: February 2024  
**Total Tables Documented**: 35
