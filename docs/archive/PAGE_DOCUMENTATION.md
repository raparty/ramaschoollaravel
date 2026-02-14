# School ERP System - Page Documentation

## 📖 Complete Page-by-Page Guide

This document provides detailed information about every page in the School ERP System, organized by module.

---

## 🏠 Core Pages

### Dashboard (dashboard.php)
**Purpose**: Main landing page after login  
**Features**:
- Quick access cards to all modules
- System statistics overview
- Recent activities summary
- Fluent UI design with Azure Blue cards

**Access**: All authenticated users

---

## 👨‍🎓 Student Management Module

### 1. Student List (student_detail.php)
**Purpose**: View all registered students  
**Features**:
- Paginated student list
- Search and filter options
- Quick actions: View, Edit, Delete
- Export functionality

**Key Columns**: Reg No, Name, Class, Section, Photo, Actions

### 2. Add New Admission (add_admission.php)
**Purpose**: Register new student  
**Form Fields**:
- Registration Number (auto-validated)
- Student Name
- Date of Birth
- Gender
- Blood Group
- Class Selection
- Admission Date
- Aadhaar Number
- Aadhaar Document Upload
- Guardian Name
- Guardian Phone
- Past School Information
- Student Photo Upload

**Validations**:
- Unique registration number
- Valid phone number format
- Age validation for class
- Required field checks

**Process Flow**:
1. Enter student details
2. Upload documents
3. Submit to `admission_process.php`
4. Redirect to student list with success message

### 3. Edit Admission (edit_admission.php)
**Purpose**: Modify existing student details  
**URL Parameter**: `?student_id=XX`  
**Features**:
- Pre-populated form with existing data
- Update student photo
- Replace documents
- Edit all student information

**Note**: Cannot change registration number once assigned

### 4. View Student Detail (view_student_detail.php)
**Purpose**: Display complete student profile  
**URL Parameter**: `?student_id=XX`  
**Sections**:
- Personal Information
- Guardian Details
- Academic Information
- Fee Status
- Photo Display
- Document Links

**Action Buttons**:
- Edit Profile (→ edit_admission.php)
- View Fees (→ fees_manager.php)
- Generate TC (→ entry_student_tc.php)
- Back to List

### 5. Student Search (searchby_name.php)
**Purpose**: Advanced student search  
**Search Criteria**:
- By Name (partial match)
- By Registration Number
- By Class
- By Section
- By Admission Date Range

**Results**: Displayed in student_search_result.php

### 6. Transfer Certificate (entry_student_tc.php)
**Purpose**: Generate TC for leaving students  
**URL Parameter**: `?student_id=XX`  
**Form Fields**:
- Date of Leaving
- Class at Leaving
- Reason for Leaving
- Conduct
- Remarks

**Output**: Generates printable TC (student_tc_show.php)

### 7. RTE Student Management (rte_student_detail.php)
**Purpose**: Manage Right to Education quota students  
**Features**:
- Separate tracking for RTE students
- Similar functionality to regular students
- Additional RTE-specific fields
- Reports for government compliance

**Related Pages**:
- rte_admission.php (Add RTE student)
- rte_edit_admission.php (Edit RTE student)
- rte_view_student_detail.php (View RTE student)

---

## 💰 Fees Management Module

### 1. Fees Manager (fees_manager.php)
**Purpose**: Central fees management dashboard  
**Features**:
- Student fee records list
- Pending fees summary
- Quick payment collection
- Fee receipt generation

**Actions**:
- View Receipt
- Collect Fee
- Edit Fee Structure
- Delete Record

### 2. Fee Package Setup (fees_package.php)
**Purpose**: Configure fee structures  
**Features**:
- Create fee packages (e.g., "Class 1-5 General")
- Define fee components (Tuition, Books, etc.)
- Set amounts for each component
- Assign to classes

**Related Pages**:
- add_fees_package.php (Create new)
- edit_fees_package.php (Modify existing)

### 3. Assign Student Fees (entry_student_fees.php)
**Purpose**: Assign fee package to student  
**Process**:
1. Search student
2. Select fee package
3. Set academic term
4. Assign fees

**Related**: add_student_fees.php, edit_student_fees.php

### 4. Collect Fees (entry_fees_reciept.php)
**Purpose**: Record fee payment  
**Features**:
- Student lookup
- Select term
- Payment amount entry
- Receipt generation
- Multiple payment modes

**Search Options**:
- By Registration Number
- By Name (fees_searchby_name.php)

### 5. Fee Receipt Display (fees_reciept.php)
**Purpose**: View and print fee receipt  
**URL Parameter**: `?receipt_id=XX`  
**Contents**:
- School header
- Student details
- Payment breakdown
- Receipt number and date
- Payment mode
- Print button

### 6. Pending Fees Report (student_pending_fees_detail.php)
**Purpose**: Track unpaid fees  
**Features**:
- List all pending fees by term
- Total pending amount
- Filter by class/section
- Export to Excel

### 7. Fine Management (student_fine_detail.php)
**Purpose**: Manage late payment fines  
**Features**:
- Add fine to student account
- Fine calculation rules
- Fine payment collection
- Fine report generation

**Related**: entry_student_fine_detail.php

### 8. Fee Reports (student_fees_reports.php)
**Purpose**: Generate fee collection reports  
**Report Types**:
- Term-wise collection
- Class-wise collection
- Date range reports
- Payment mode summary

---

## 💼 Staff Management Module

### 1. Staff List (view_staff.php)
**Purpose**: View all employees  
**Features**:
- Staff directory
- Search functionality
- Filter by category/department
- Quick view profile

### 2. Add New Staff (add_new_staff_detail.php)
**Purpose**: Register new employee  
**Form Sections**:
- Personal Information
- Contact Details
- Employment Details
- Qualification
- Document Upload
- Photo Upload

**Fields**:
- Employee ID
- Name, DOB, Gender
- Email, Phone, Address
- Category (Teaching/Non-teaching)
- Department
- Position
- Joining Date
- Salary Details
- Qualifications
- Experience

### 3. Edit Staff (edit_staf_employee_detail.php)
**Purpose**: Modify employee details  
**URL Parameter**: `?employee_id=XX`

### 4. View Staff Profile (view_staff_employee.php)
**Purpose**: Display complete employee profile  
**URL Parameter**: `?employee_id=XX`  
**Sections**:
- Personal Info
- Contact Info
- Employment Details
- Qualification
- Documents
- Photo

### 5. Staff Categories (staff_category.php)
**Purpose**: Manage staff categories  
**Examples**: Teaching, Non-Teaching, Administrative, Support  
**Actions**: Add, Edit, Delete categories

**Related**: add_staff_category.php, edit_staff_category.php

### 6. Staff Departments (staff_department.php)
**Purpose**: Manage organizational departments  
**Examples**: Primary, Secondary, Administration, Maintenance  
**Actions**: Add, Edit, Delete departments

### 7. Staff Positions (staff_position.php)
**Purpose**: Manage job positions  
**Examples**: Principal, Vice Principal, Teacher, Peon, Clerk  
**Actions**: Add, Edit, Delete positions

### 8. Staff Qualifications (qualification.php)
**Purpose**: Manage qualification types  
**Examples**: B.Ed, M.Ed, B.A., M.A., Ph.D.  
**Actions**: Add, Edit, Delete qualifications

---

## 📚 Library Management Module

### 1. Library Dashboard (library.php)
**Purpose**: Central library management  
**Quick Links**:
- Book Management
- Issue/Return
- Categories
- Fine Management
- Reports

### 2. Book Management (library_book_manager.php)
**Purpose**: Manage library inventory  
**Features**:
- Book catalog
- Book details (Title, Author, ISBN, etc.)
- Stock tracking
- Available/Issued status

**Actions**: Add, Edit, Delete books

### 3. Add New Book (library_add_book.php)
**Form Fields**:
- Book Title
- Author
- ISBN
- Publisher
- Edition
- Category
- Total Copies
- Available Copies
- Purchase Date
- Price

### 4. Book Categories (library_book_category.php)
**Purpose**: Organize books by category  
**Examples**: Fiction, Non-Fiction, Reference, Textbooks, Magazines

**Related**:
- library_add_book_category.php
- library_edit_book_category.php

### 5. Issue Book (library_entry_add_student_books.php)
**Purpose**: Issue book to student  
**Process**:
1. Search student
2. Select book from available inventory
3. Set issue date
4. Set expected return date (auto-calculated)
5. Submit transaction

**Validations**:
- Book availability
- Student eligibility (no pending fines)
- Issue limit per student

### 6. Return Book (library_entry_student_return_books.php)
**Purpose**: Process book return  
**Process**:
1. Search student or scan book
2. Select issued book
3. Check return date
4. Calculate fine if overdue
5. Process return

### 7. Fine Management (library_fine_manager.php)
**Purpose**: Manage library fines  
**Features**:
- Set fine rules (per day delay)
- View pending fines
- Collect fine payments
- Fine reports

**Related**: library_add_fine.php

### 8. Student Book History (library_student_books_manager.php)
**Purpose**: View student's library transactions  
**URL Parameter**: `?student_id=XX`  
**Shows**:
- Current issued books
- Return history
- Fine history

---

## 🚌 Transport Management Module

### 1. Transport Dashboard (transport.php)
**Purpose**: Central transport management  
**Quick Links**:
- Routes
- Vehicles
- Student Allocation
- Transport Fees

### 2. Route Management (transport_route_detail.php)
**Purpose**: Manage bus routes  
**Features**:
- Route name/number
- Pickup points
- Distance
- Fee structure per route

**Actions**: Add, Edit, Delete routes

**Related**:
- transport_add_route.php
- transport_route_edit.php

### 3. Vehicle Management (transport_vechile_detail.php)
**Purpose**: Manage school vehicles  
**Features**:
- Vehicle registration
- Vehicle type (Bus, Van, etc.)
- Capacity
- Driver details
- Conductor details
- Maintenance records

**Related**:
- transport_add_vechile.php
- transport_edit_vehicle.php

### 4. Student Transport Allocation (transport_student_detail.php)
**Purpose**: Assign students to routes  
**Features**:
- Student list by route
- Pickup point assignment
- Fee calculation based on route

**Related**:
- transport_add_student.php
- transport_edit_student.php

### 5. Transport Fees (student_transport_fees.php)
**Purpose**: Manage transport fee structure  
**Features**:
- Route-wise fees
- Term-wise collection
- Fee reports

**Related**:
- add_student_transport_fees.php
- edit_student_transport_fees.php

### 6. Collect Transport Fees (entry_transport_fees_reciept.php)
**Purpose**: Record transport fee payment  
**Process**: Similar to regular fee collection

**Related**: transport_fees_reciept.php (receipt display)

### 7. Transport Fee Reports
**Purpose**: Generate transport-specific reports  
**Reports**:
- Collection summary
- Pending fees
- Route-wise analysis

---

## 💵 Accounts Management Module

### 1. Accounts Dashboard (account_report.php)
**Purpose**: Financial overview  
**Features**:
- Income vs Expense summary
- Monthly trends
- Category-wise breakdown
- Date range filtering

### 2. Income Management (income_manager.php)
**Purpose**: Track school income  
**Features**:
- Income entry list
- Category filtering
- Amount summary
- Actions: Add, Edit, Delete

**Income Categories**:
- Fee Collection
- Donations
- Grants
- Other Income

**Related**:
- add_income.php
- edit_income.php

### 3. Add Income (add_income.php)
**Form Fields**:
- Date
- Category
- Sub-category
- Amount
- Payment Mode
- Reference Number
- Narration/Description

### 4. Expense Management (expense_manager.php)
**Purpose**: Track school expenses  
**Features**:
- Expense entry list
- Category filtering
- Amount summary
- Actions: Add, Edit, Delete

**Expense Categories**:
- Salary
- Utilities
- Maintenance
- Supplies
- Other Expenses

**Related**:
- add_expense.php
- edit_expense.php

### 5. Category Management (account_category_manager.php)
**Purpose**: Manage income/expense categories  
**Features**:
- Add main categories
- Add sub-categories
- Edit categories
- Delete unused categories

**Related**:
- add_account_category_manager.php
- edit_account_category_manager.php

### 6. Daily Report (daily_report.php)
**Purpose**: Daily transaction summary  
**Features**:
- Date selection
- Income list for the day
- Expense list for the day
- Net balance
- Print functionality

**Related**: print_daily_report.php

---

## 🎓 Academic Management Module

### 1. Class Management (class.php)
**Purpose**: Configure academic classes  
**Features**:
- List of classes (e.g., Class 1, Class 2, etc.)
- View sections per class
- Add, Edit, Delete classes

**Related**:
- add_class.php
- edit_class.php

### 2. Section Management (section.php)
**Purpose**: Manage class sections  
**Features**:
- Section list (A, B, C, etc.)
- Assign to classes
- Actions: Add, Edit, Delete

**Related**:
- add_section.php
- edit_section.php

### 3. Stream Management (stream.php)
**Purpose**: Manage academic streams  
**Examples**: Science, Commerce, Arts (for higher classes)  
**Features**: Add, Edit, Delete streams

**Related**:
- add_stream.php
- edit_stream.php

### 4. Subject Management (subject.php)
**Purpose**: Manage subjects  
**Features**:
- Subject list
- Subject code
- Add, Edit, Delete subjects

**Related**:
- add_subject.php
- edit_subject.php

### 5. Allocate Stream (allocate_stream.php)
**Purpose**: Assign streams to classes  
**Example**: Assign Science stream to Class 11  
**Features**: View allocations, Add, Edit, Delete

**Related**:
- add_allocate_stream.php
- edit_allocate_stream.php

### 6. Allocate Section (allocate_section.php)
**Purpose**: Assign sections to classes  
**Example**: Assign sections A, B, C to Class 5  
**Features**: View allocations, Add, Edit, Delete

**Related**:
- add_allocate_section.php
- edit_allocate_section.php

### 7. Allocate Subject (allocate_subject.php)
**Purpose**: Assign subjects to class/stream combinations  
**Example**: Assign Physics, Chemistry to Class 11 Science  
**Features**: View allocations, Add, Edit, Delete

**Related**:
- add_allocate_subject.php
- edit_allocate_subject.php

---

## 📝 Examination Module

### 1. Exam Settings (exam_setting.php)
**Purpose**: Configure examination system  
**Links**:
- Exam Terms
- Maximum Marks
- Time Table
- Marks Entry

### 2. Term Manager (term_manager.php)
**Purpose**: Manage examination terms  
**Examples**: First Term, Mid Term, Final Term, Annual Exam  
**Features**: Add, Edit, Delete terms

**Related**:
- add_term.php
- edit_term.php

### 3. Exam Date/Time Table (exam_date.php)
**Purpose**: Create exam schedule  
**Features**:
- Select term
- Select class/section
- Subject-wise date and time
- Hall allocation

**Related**: exam_edit_time_table.php

### 4. View Time Table (exam_time_table_detail.php)
**Purpose**: Display exam schedule  
**Filter Options**:
- By Term
- By Class
- By Date

### 5. Maximum Marks Setup (exam_show_maximum_marks.php)
**Purpose**: Define maximum marks for subjects  
**Features**:
- Subject-wise max marks
- Theory/Practical breakdown
- Internal/External marks

**Related**:
- exam_add_maximum_marks.php
- exam_edit_maximum_marks.php

### 6. Marks Entry (exam_marks_add_student.php)
**Purpose**: Enter examination marks  
**Process**:
1. Select term
2. Select class/section
3. Select subject
4. Enter marks for each student
5. Save marks

**Related**: entry_exam_add_student_marks.php

### 7. View Student Marks (exam_show_student_marks.php)
**Purpose**: Display entered marks  
**Features**:
- Term-wise marks
- Subject-wise marks
- Edit functionality

### 8. Marksheet Generation (marksheet.php)
**Purpose**: Generate student report card  
**URL Parameter**: `?student_id=XX&term_id=XX`  
**Contents**:
- Student details
- Subject-wise marks
- Total marks
- Percentage
- Grade
- Remarks
- Print functionality

**Related**: exam_final_marksheet.php

### 9. Result Processing (exam_result.php)
**Purpose**: Calculate and publish results  
**Features**:
- Grade calculation
- Pass/Fail determination
- Rank assignment
- Bulk result generation

---

## 📊 Attendance Module

### 1. Attendance Marking (Attendance.php)
**Purpose**: Mark daily attendance  
**Features**:
- Select date
- Select class/section
- Mark present/absent for each student
- Bulk marking options (Mark All Present)
- Save attendance

**Validation**: Cannot mark future dates

### 2. Attendance Reports
**Purpose**: View attendance records  
**Filter Options**:
- By Date Range
- By Class/Section
- By Student
- Monthly summary

---

## ⚙️ Settings & Configuration

### 1. School Settings (school_setting.php)
**Purpose**: Configure system-wide settings  
**Links**:
- School Details
- Academic Settings
- Fees Settings
- Library Settings
- Transport Settings
- Staff Settings

### 2. School Details (school_detail.php)
**Purpose**: Manage school information  
**Fields**:
- School Name
- Address
- Phone, Email
- Website
- Registration Number
- Affiliation
- Logo Upload

**Related**:
- add_school_detail.php
- edit_school_detail.php

### 3. Change Password (change_password.php)
**Purpose**: Update user password  
**Fields**:
- Current Password
- New Password
- Confirm New Password

**Validation**: Password strength requirements

---

## 🔐 Authentication Pages

### 1. Login Page (index.php)
**Purpose**: System login  
**Fields**:
- Username/Email
- Password
- Remember Me (optional)

**Process**: Validates credentials via login_process.php

### 2. Logout (logout.php)
**Purpose**: End user session  
**Process**: 
- Destroys session
- Redirects to login page

---

## 🔍 Search & Utility Pages

### 1. Search by Name (various modules)
- fees_searchby_name.php (Fees search)
- transport_searchby_name.php (Transport search)
- library_student_searchby_name.php (Library search)
- exam_searchby_name.php (Exam search)

**Common Features**:
- Partial name matching
- Quick results
- Direct action links

### 2. AJAX Handlers
- ajaxcode.php (General AJAX)
- ajax_fees_code.php (Fee calculations)
- ajax_stream_code.php (Stream data)
- ajax_transport_fees_code.php (Transport fee calculations)
- vehicle_ajax.php (Vehicle lookup)

### 3. Validation Pages
- checkregno.php (Registration number validation)
- checkbookno.php (Book number validation)
- rte_checkregno.php (RTE reg number validation)

---

## 📄 Common Page Elements

### Header (includes/header.php)
**Contains**:
- School logo
- Navigation menu
- User info
- Logout button
- Breadcrumb navigation

### Footer (includes/footer.php)
**Contains**:
- Copyright info
- Version number
- Quick links
- JavaScript includes

### Sidebar Navigation
**Modules**:
- Dashboard
- Students
- Fees
- Staff
- Accounts
- Library
- Transport
- Academics
- Examinations
- Attendance
- Settings

---

## 🎨 Design Patterns Used

### 1. List-Detail Pattern
Most modules follow this pattern:
- List page (e.g., student_detail.php)
- Detail page (e.g., view_student_detail.php)
- Add page (e.g., add_admission.php)
- Edit page (e.g., edit_admission.php)

### 2. Search-Result Pattern
- Entry page with search form
- Result page with filtered data
- Action links to detail/edit

### 3. Master-Detail Pattern
- Master configuration (e.g., fees_package.php)
- Detail assignment (e.g., add_student_fees.php)

---

## 🔄 Common Workflows

### Add New Record
1. Click "Add New" button
2. Fill form
3. Validate required fields
4. Submit
5. Process in handler page
6. Redirect to list with success message

### Edit Record
1. Click "Edit" icon on list page
2. Pre-populate form with existing data
3. Modify fields
4. Submit
5. Update in database
6. Redirect back to list

### Delete Record
1. Click "Delete" icon
2. Confirm deletion (JavaScript prompt)
3. Delete from database
4. Redirect with success message

### Search Record
1. Enter search criteria
2. Submit search form
3. Display filtered results
4. Provide action links on results

---

## 📱 Responsive Behavior

### Desktop (> 1024px)
- Full sidebar visible
- Multi-column layouts
- Expanded tables

### Tablet (768px - 1024px)
- Collapsible sidebar
- Adjusted column widths
- Horizontal scroll for tables

### Mobile (< 768px)
- Hidden sidebar (hamburger menu)
- Single column layout
- Stacked form fields
- Simplified tables

---

## 🔧 Technical Notes

### Database Connection
All pages include: `require_once 'includes/bootstrap.php';`  
This initializes database connection and session.

### Authentication Check
Protected pages include: `require_once 'includes/header.php';`  
This checks for valid session.

### File Upload Handling
- Student photos: uploads/students/
- Aadhaar documents: uploads/aadhaar/
- Staff photos: uploads/staff/

### Date Format
- Display: DD-MM-YYYY
- Database: YYYY-MM-DD
- JavaScript: ISO format

### Number Format
- Currency: ₹ XX,XXX.XX
- Decimals: 2 places for amounts

---

**Document Version**: 1.0  
**Total Pages Documented**: 150+  
**Last Updated**: February 2024
