# PHP to Laravel 10 Migration Guide
## School ERP System

This document outlines the comprehensive migration plan for converting the existing procedural PHP School ERP application to Laravel 10 with proper MVC architecture.

## Current Application Analysis

### **Application Statistics**
- **Total PHP Files**: 278 procedural files
- **Database Tables**: 40+ tables (MySQL 8.4+)
- **Key Modules**: Students, Fees, Library, Transport, Staff, Exams, Accounts, Attendance, Reports

### **Current Architecture**
- Procedural PHP with mixed HTML/SQL/business logic
- Manual session management
- Raw MySQL queries using mysqli
- Basic RBAC implementation in `role_permissions` table
- File uploads to `/uploads` directory
- Manual form handling without CSRF protection

### **Database Schema Overview**
#### Core Tables Identified:
- **Authentication**: `users`, `role_permissions`, `permissions`
- **Students**: `admissions`, `student_fees_detail`, `student_transport_fees_detail`
- **Academic**: `classes`, `section`, `streams`, `subjects`, `allocate_*` tables
- **Library**: `library_books`, `library_book_category`, `library_student_books`, `library_fines`
- **Transport**: `transport_routes`, `transport_vehicles`, `transport_students`
- **Staff**: `staff_detail`, `staff_category`, `staff_department`, `staff_position`
- **Exams**: `exam_terms`, `exam_maximum_marks`, `exam_student_marks`
- **Accounts**: `account_category`, `account_exp_income_detail`
- **System**: `session` (academic session/year management)

## Migration Strategy

### **Phase-wise Approach**
This migration will be done incrementally to ensure the existing system remains operational during transition.

---

## Phase 1: Laravel Setup & Infrastructure ✅ (IN PROGRESS)

### Objectives:
- Set up Laravel 10 installation alongside existing application
- Configure database connection to existing schema
- Maintain backward compatibility with existing PHP files

### Tasks:
1. **Install Laravel 10**
   - ✅ Created Laravel instance in `/laravel-app` directory
   - ✅ Added to `.gitignore` to prevent accidental commits during development
   - ⏳ Complete vendor dependencies installation

2. **Configure Environment**
   ```bash
   # Update laravel-app/.env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=school_erp_db
   DB_USERNAME=erp_admin
   DB_PASSWORD=SchoolERP@2026
   ```

3. **Directory Structure**
   ```
   /home/runner/work/ramaschoollaravel/
   ├── ramaschoollaravel/          # Existing PHP application (unchanged)
   │   ├── index.php
   │   ├── dashboard.php
   │   ├── add_admission.php
   │   ├── includes/
   │   ├── db/
   │   └── ...
   └── laravel-app/                # New Laravel 10 installation
       ├── app/
       ├── routes/
       ├── resources/
       └── ...
   ```

4. **Testing Laravel Installation**
   - Test database connectivity
   - Generate application key
   - Verify artisan commands work

### Deliverables:
- [ ] Working Laravel installation
- [ ] Database connectivity confirmed
- [ ] Environment configuration complete

---

## Phase 2: Authentication & RBAC

### Objectives:
- Migrate authentication from procedural to Laravel Auth
- Implement role-based access control using Laravel's authorization
- Maintain compatibility with existing `users` and `role_permissions` tables

### Tasks:

1. **Create User Model**
   ```php
   // app/Models/User.php
   - Map to existing 'users' table
   - Include role relationship
   - Password verification using password_verify()
   ```

2. **Create Role & Permission Models**
   ```php
   // app/Models/Role.php
   // app/Models/Permission.php
   // app/Models/RolePermission.php
   ```

3. **Implement Authentication**
   - Use Laravel's built-in Auth system
   - Create LoginController
   - Create login Blade view
   - Add CSRF protection to login form

4. **Implement Authorization Gates & Policies**
   ```php
   // app/Providers/AuthServiceProvider.php
   - Define gates for each module (students, fees, library, etc.)
   - Map to existing role_permissions table
   ```

5. **Create Middleware**
   ```php
   // app/Http/Middleware/RoleMiddleware.php
   - Check user roles before route access
   - Redirect unauthorized users
   ```

### Deliverables:
- [ ] User, Role, Permission Eloquent models
- [ ] Laravel Auth implementation
- [ ] Authorization gates and policies
- [ ] Role-based middleware
- [ ] Login/Logout functionality migrated

---

## Phase 3: Student Module Migration

### Objectives:
- Convert student management from procedural to MVC
- Create Eloquent models for student-related tables
- Implement CRUD operations with validation

### Tables to Model:
- `admissions`
- `student_fees_detail`
- `student_transport_fees_detail`
- `student_tc` (Transfer Certificate)

### Tasks:

1. **Create Eloquent Models**
   ```php
   // app/Models/Admission.php
   // app/Models/StudentFee.php
   // app/Models/StudentTransportFee.php
   // app/Models/StudentTC.php
   ```

2. **Create Controllers**
   ```php
   // app/Http/Controllers/AdmissionController.php
   - index() - list admissions
   - create() - show admission form
   - store() - save new admission
   - show() - view single admission
   - edit() - show edit form
   - update() - update admission
   - destroy() - delete admission
   
   // app/Http/Controllers/StudentFeeController.php
   // app/Http/Controllers/StudentTransportController.php
   // app/Http/Controllers/StudentTCController.php
   ```

3. **Create Form Requests for Validation**
   ```php
   // app/Http/Requests/StoreAdmissionRequest.php
   - Validate registration number uniqueness
   - Validate required fields
   - Validate file uploads (student_pic, aadhaar_doc)
   ```

4. **Create Blade Views**
   ```php
   // resources/views/admissions/
   - index.blade.php (list)
   - create.blade.php (form - converts add_admission.php)
   - edit.blade.php (edit form - converts edit_admission.php)
   - show.blade.php (detail view - converts view_student_detail.php)
   ```

5. **Define Routes**
   ```php
   // routes/web.php
   Route::middleware(['auth'])->group(function () {
       Route::resource('admissions', AdmissionController::class);
       Route::resource('student-fees', StudentFeeController::class);
   });
   ```

6. **File Upload Handling**
   - Store student photos in `storage/app/public/students/photos`
   - Store Aadhaar documents in `storage/app/public/students/aadhaar`
   - Create symbolic link: `php artisan storage:link`

### Files to Convert:
- `add_admission.php` → `AdmissionController@create`
- `admission_process.php` → `AdmissionController@store`
- `edit_admission.php` → `AdmissionController@edit`
- `process_edit_admission.php` → `AdmissionController@update`
- `delete_admission.php` → `AdmissionController@destroy`
- `student_detail.php` → `AdmissionController@index`
- `view_student_detail.php` → `AdmissionController@show`
- `searchby_name.php` → Search functionality

### Deliverables:
- [ ] Student Eloquent models
- [ ] Student CRUD controllers
- [ ] Form validation requests
- [ ] Blade templates for student management
- [ ] Routes defined
- [ ] File upload configured

---

## Phase 4: Fees Module Migration

### Objectives:
- Convert fee management to Laravel
- Implement fee package, collection, and receipt generation

### Tables to Model:
- `fees_packages`
- `student_fees_detail`
- `student_transport_fees_detail`
- `terms`

### Tasks:

1. **Create Models**
   ```php
   // app/Models/FeePackage.php
   // app/Models/StudentFee.php
   // app/Models/Term.php
   ```

2. **Create Controllers**
   ```php
   // app/Http/Controllers/FeeController.php
   - managePackages()
   - collectFee()
   - generateReceipt()
   - pendingFees()
   - feeReports()
   ```

3. **Create Blade Views**
   - Fee package management
   - Fee collection forms
   - Receipt generation
   - Pending fees reports

4. **Receipt Generation**
   - Use Laravel PDF package (barryvdh/laravel-dompdf)
   - Create receipt template
   - Generate downloadable PDFs

### Files to Convert:
- `add_fees_package.php` → `FeeController@createPackage`
- `add_student_fees.php` → `FeeController@collectFee`
- `fees_reciept.php` → `FeeController@receipt`
- `student_pending_fees_detail.php` → `FeeController@pendingFees`
- `entry_fees_reciept.php` → Fee entry forms

### Deliverables:
- [ ] Fee Eloquent models
- [ ] Fee controllers
- [ ] Blade views for fee management
- [ ] PDF receipt generation
- [ ] Routes for fee operations

---

## Phase 5: Library Module Migration

### Objectives:
- Convert library management system to Laravel
- Implement book issue/return tracking
- Fine management

### Tables to Model:
- `library_books`
- `library_book_category`
- `library_student_books`
- `library_fines`

### Tasks:

1. **Create Models**
   ```php
   // app/Models/Library/Book.php
   // app/Models/Library/BookCategory.php
   // app/Models/Library/StudentBook.php
   // app/Models/Library/Fine.php
   ```

2. **Create Controllers**
   ```php
   // app/Http/Controllers/Library/BookController.php
   // app/Http/Controllers/Library/IssueReturnController.php
   // app/Http/Controllers/Library/FineController.php
   ```

3. **Implement Business Logic**
   - Book availability checking
   - Automatic fine calculation on overdue books
   - Book return process
   - Fine payment tracking

4. **Create Blade Views**
   - Book catalog
   - Book issue form
   - Book return form
   - Fine management
   - Library reports

### Files to Convert:
- `library_add_book.php` → `BookController@create`
- `library_add_student_books.php` → `IssueReturnController@issue`
- `library_return_student_books.php` → `IssueReturnController@return`
- `library_add_fine.php` → `FineController@store`
- `library_book_manager.php` → `BookController@index`

### Deliverables:
- [ ] Library models
- [ ] Library controllers
- [ ] Book issue/return workflow
- [ ] Fine calculation and management
- [ ] Blade templates

---

## Phase 6: Attendance Module Migration

### Objectives:
- Convert attendance management to Laravel
- Implement daily attendance marking
- Generate attendance reports

### Tables to Model:
- `attendance` (if exists, or create migration)

### Tasks:

1. **Create Migration** (if table doesn't exist)
   ```php
   // database/migrations/xxxx_create_attendance_table.php
   ```

2. **Create Model**
   ```php
   // app/Models/Attendance.php
   ```

3. **Create Controller**
   ```php
   // app/Http/Controllers/AttendanceController.php
   - markAttendance()
   - viewAttendance()
   - attendanceReports()
   ```

4. **Create Blade Views**
   - Attendance marking form
   - Attendance register
   - Attendance reports

### Files to Convert:
- `Attendance.php` → `AttendanceController`
- `attendance_register.php` → Attendance views
- `attendance_report.php` → Report generation

### Deliverables:
- [ ] Attendance model and migration (if needed)
- [ ] Attendance controller
- [ ] Blade views for attendance
- [ ] Report generation

---

## Phase 7: Staff Module Migration

### Objectives:
- Convert staff management to Laravel
- Manage staff categories, departments, positions, qualifications

### Tables to Model:
- `staff_detail`
- `staff_category`
- `staff_department`
- `staff_position`
- `staff_qualification`

### Tasks:

1. **Create Models**
   ```php
   // app/Models/Staff/StaffDetail.php
   // app/Models/Staff/StaffCategory.php
   // app/Models/Staff/StaffDepartment.php
   // app/Models/Staff/StaffPosition.php
   // app/Models/Staff/StaffQualification.php
   ```

2. **Create Controllers**
   ```php
   // app/Http/Controllers/Staff/StaffController.php
   // app/Http/Controllers/Staff/StaffCategoryController.php
   // app/Http/Controllers/Staff/StaffDepartmentController.php
   ```

3. **Create Blade Views**
   - Staff listing
   - Add/Edit staff
   - Staff profile view
   - Department/Position/Category management

### Files to Convert:
- `add_new_staff_detail.php` → `StaffController@create`
- `edit_staf_employee_detail.php` → `StaffController@edit`
- `view_staff_employee.php` → `StaffController@show`
- `employee_profile.php` → Staff profile view

### Deliverables:
- [ ] Staff models
- [ ] Staff controllers
- [ ] Staff management Blade views
- [ ] Routes for staff operations

---

## Phase 8: Exams & Reports Module

### Objectives:
- Convert exam management system
- Implement marksheet generation
- Create comprehensive reports

### Tables to Model:
- `exam_terms`
- `exam_time_table`
- `exam_maximum_marks`
- `exam_student_marks`

### Tasks:

1. **Create Models**
   ```php
   // app/Models/Exam/ExamTerm.php
   // app/Models/Exam/ExamTimeTable.php
   // app/Models/Exam/ExamMaximumMarks.php
   // app/Models/Exam/ExamStudentMarks.php
   ```

2. **Create Controllers**
   ```php
   // app/Http/Controllers/Exam/ExamController.php
   // app/Http/Controllers/Exam/MarksheetController.php
   // app/Http/Controllers/Exam/ReportController.php
   ```

3. **Implement Features**
   - Exam schedule management
   - Marks entry
   - Marksheet generation (PDF)
   - Result compilation
   - Pass/Fail calculation

4. **Create Blade Views**
   - Exam scheduling
   - Marks entry forms
   - Marksheet templates
   - Result reports

### Files to Convert:
- `exam_setting.php` → Exam management
- `exam_add_maximum_marks.php` → Marks setup
- `exam_marks_add_student.php` → Marks entry
- `marksheet.php` → Marksheet generation
- `exam_result.php` → Results

### Deliverables:
- [ ] Exam models
- [ ] Exam controllers
- [ ] Marksheet PDF generation
- [ ] Result reports
- [ ] Blade templates

---

## Phase 9: Transport & Accounts Modules

### Objectives:
- Convert transport management
- Implement accounting module

### Transport Tables:
- `transport_routes`
- `transport_vehicles`
- `transport_students`

### Accounts Tables:
- `account_category`
- `account_exp_income_detail`
- `accounts`

### Tasks:

1. **Create Models for Transport & Accounts**

2. **Create Controllers**

3. **Create Blade Views**

4. **Implement Business Logic**
   - Route management
   - Vehicle tracking
   - Student transport fee tracking
   - Income/Expense recording
   - Financial reports

### Deliverables:
- [ ] Transport models and controllers
- [ ] Accounts models and controllers
- [ ] Blade views
- [ ] Financial reporting

---

## Phase 10: Additional Features & Optimization

### Tasks:

1. **Academic Management**
   - Classes, Sections, Streams, Subjects
   - Class-Section-Subject allocation
   
2. **Session Management**
   - Academic year/session handling
   - Session-wise data filtering

3. **Dashboard Migration**
   - Convert dashboard.php to Laravel
   - Display statistics and quick links
   - Modern UI components

4. **Search Functionality**
   - Global search across modules
   - Student search
   - Staff search

5. **Reports Module**
   - Comprehensive reporting system
   - Export to PDF/Excel
   - Custom date range filtering

### Deliverables:
- [ ] Academic management complete
- [ ] Session management
- [ ] Dashboard migrated
- [ ] Global search implemented
- [ ] Reports module complete

---

## Phase 11: Testing, Security & Performance

### Tasks:

1. **Security Audit**
   - CSRF protection on all forms (Laravel handles automatically)
   - XSS prevention (Blade escaping)
   - SQL injection prevention (Eloquent ORM)
   - File upload validation
   - Authentication checks on all routes

2. **Testing**
   - Feature tests for critical flows
   - Unit tests for business logic
   - Browser tests for UI

3. **Performance Optimization**
   - Query optimization
   - Eager loading relationships
   - Caching frequently accessed data
   - Asset compilation (Mix/Vite)

4. **Code Quality**
   - Follow PSR-12 coding standards
   - Laravel best practices
   - DRY principle
   - SOLID principles

### Deliverables:
- [ ] Security audit complete
- [ ] Test suite created
- [ ] Performance optimizations applied
- [ ] Code quality standards met

---

## Phase 12: Deployment & Documentation

### Tasks:

1. **Migration Path**
   - Create database migration scripts (if schema changes needed)
   - Data migration scripts (if needed)
   - Rollback plan

2. **Documentation**
   - API documentation
   - User guide
   - Developer guide
   - Deployment guide

3. **Training**
   - User training materials
   - Admin training
   - Developer handoff

4. **Go-Live Checklist**
   - Backup existing system
   - Deploy Laravel application
   - Test all modules
   - Monitor for issues
   - Gradual rollout plan

### Deliverables:
- [ ] Complete documentation
- [ ] Training materials
- [ ] Deployment scripts
- [ ] Go-live checklist

---

## Technical Guidelines

### **Laravel Best Practices to Follow**

1. **Models**
   - Use Eloquent ORM for all database operations
   - Define relationships (hasMany, belongsTo, belongsToMany)
   - Use accessors and mutators for data transformation
   - Implement model events where needed

2. **Controllers**
   - Keep controllers thin
   - Use Form Requests for validation
   - Return views or JSON responses
   - Use resource controllers for CRUD operations

3. **Validation**
   - Create Form Request classes for complex validation
   - Use validation rules in Form Requests
   - Display validation errors in Blade views

4. **Blade Templates**
   - Use Blade directives (@if, @foreach, @auth, @can)
   - Create reusable components
   - Use layouts and sections
   - Escape output with {{ }} (automatic XSS prevention)

5. **Routing**
   - Group routes by middleware
   - Use resource routes where appropriate
   - Name all routes for easy linking
   - Use route model binding

6. **Database**
   - No migrations needed (using existing schema)
   - Use seeders if initial data is needed
   - Use factories for testing

7. **File Storage**
   - Use Laravel's Storage facade
   - Store in storage/app/public
   - Create symbolic link with `php artisan storage:link`

8. **Security**
   - CSRF protection (automatic with forms)
   - Authorization gates and policies
   - Input validation on all forms
   - Use parameterized queries (Eloquent does this)

### **Migration Principles**

1. **Incremental Migration**: Migrate one module at a time
2. **Backward Compatibility**: Keep existing PHP files working during migration
3. **Database Preservation**: Use existing database schema without modifications
4. **Testing Each Phase**: Test thoroughly before moving to next phase
5. **User Training**: Train users on new interface gradually

### **Code Organization**

```
laravel-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdmissionController.php
│   │   │   ├── FeeController.php
│   │   │   ├── Library/
│   │   │   ├── Staff/
│   │   │   └── Exam/
│   │   ├── Requests/
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Admission.php
│   │   ├── Library/
│   │   ├── Staff/
│   │   └── Exam/
│   ├── Policies/
│   └── Providers/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── admissions/
│       ├── fees/
│       ├── library/
│       ├── staff/
│       └── exams/
└── routes/
    └── web.php
```

---

## Success Criteria

1. **Functional Parity**: All features from existing system work in Laravel
2. **Security Improved**: CSRF, XSS, SQL injection protections in place
3. **Performance**: Application performs as fast or faster than existing
4. **Code Quality**: Clean, maintainable, following Laravel standards
5. **User Experience**: Modern UI, responsive design
6. **Documentation**: Complete documentation for maintenance
7. **Testing**: Adequate test coverage for critical features

---

## Timeline Estimate

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Phase 1: Setup | 1-2 days | None |
| Phase 2: Auth & RBAC | 2-3 days | Phase 1 |
| Phase 3: Students | 3-5 days | Phase 2 |
| Phase 4: Fees | 3-5 days | Phase 2, 3 |
| Phase 5: Library | 3-4 days | Phase 2, 3 |
| Phase 6: Attendance | 2-3 days | Phase 2, 3 |
| Phase 7: Staff | 3-4 days | Phase 2 |
| Phase 8: Exams | 4-5 days | Phase 2, 3 |
| Phase 9: Transport & Accounts | 4-5 days | Phase 2 |
| Phase 10: Additional Features | 3-4 days | All previous |
| Phase 11: Testing & Security | 3-5 days | All previous |
| Phase 12: Deployment | 2-3 days | Phase 11 |
| **TOTAL** | **30-45 days** | |

*Note: Timeline is approximate and depends on team size, expertise, and complexity discovered during implementation*

---

## Current Status

- **Phase 1**: IN PROGRESS
  - ✅ Laravel 10 installed in /laravel-app directory
  - ✅ Added to .gitignore
  - ⏳ Complete vendor installation
  - ⏳ Configure .env for database
  - ⏳ Test artisan commands

---

## Next Steps

1. Complete Laravel vendor installation
2. Configure database connection
3. Test Laravel-database connectivity
4. Begin Phase 2: Authentication migration

---

## Notes

- This is a **step-by-step migration**, not an automatic rewrite
- Existing PHP application will remain operational during migration
- Each phase should be tested thoroughly before proceeding
- Database schema remains unchanged to maintain compatibility
- Focus on clean, maintainable Laravel code
- Follow Laravel conventions and best practices

---

**Document Version**: 1.0
**Last Updated**: February 14, 2026
**Status**: Migration Planning Complete, Phase 1 In Progress
