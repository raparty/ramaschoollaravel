<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\FeePackageController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\StaffAttendanceController;
use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AccountReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\StaffLeaveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes (Phase 2)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // Student Admissions (Phase 3)
    Route::resource('admissions', AdmissionController::class);
    Route::get('/admissions/search', [AdmissionController::class, 'search'])->name('admissions.search');
    Route::get('/admissions/{admission}/check-regno', [AdmissionController::class, 'checkRegNo'])->name('admissions.check-regno');
    
    // Transfer Certificate (under students)
    Route::get('/students/transfer-certificate', [App\Http\Controllers\TransferCertificateController::class, 'index'])->name('students.transfer-certificate.index');
    Route::get('/students/transfer-certificate/search', [App\Http\Controllers\TransferCertificateController::class, 'search'])->name('students.transfer-certificate.search');
    Route::get('/students/transfer-certificate/show-by-regno', [App\Http\Controllers\TransferCertificateController::class, 'showByRegNo'])->name('students.transfer-certificate.show-by-regno');
    Route::get('/students/transfer-certificate/{regNo}', [App\Http\Controllers\TransferCertificateController::class, 'show'])->name('students.transfer-certificate.show');

    // Fee Management (Phase 4)
    Route::resource('fee-packages', FeePackageController::class);
    
    // Fee Collection Routes
    Route::get('/fees/search', [FeeController::class, 'search'])->name('fees.search');
    Route::get('/fees/collect', [FeeController::class, 'collect'])->name('fees.collect');
    Route::post('/fees/store', [FeeController::class, 'store'])->name('fees.store');
    Route::get('/fees/receipt', [FeeController::class, 'receipt'])->name('fees.receipt');
    Route::get('/fees/pending', [FeeController::class, 'pending'])->name('fees.pending');
    Route::get('/fees/search-students', [FeeController::class, 'searchStudents'])->name('fees.search-students');
    
    // Library Management (Phase 5 / Phase B Week 1-2)
    Route::prefix('library')->name('library.')->group(function () {
        // Book Management
        Route::resource('books', LibraryController::class);
        Route::get('/books-search', [LibraryController::class, 'search'])->name('books.search');
        
        // Book Issue/Return
        Route::get('/issue', [BookIssueController::class, 'issueForm'])->name('issue.create');
        Route::post('/issue', [BookIssueController::class, 'issueBook'])->name('issue.store');
        Route::get('/return', [BookIssueController::class, 'returnForm'])->name('issue.return');
        Route::post('/return', [BookIssueController::class, 'returnBook'])->name('issue.process-return');
        Route::get('/history', [BookIssueController::class, 'studentHistory'])->name('issue.history');
        Route::get('/overdue', [BookIssueController::class, 'overdueList'])->name('issue.overdue');
        Route::post('/collect-fine', [BookIssueController::class, 'collectFine'])->name('issue.collect-fine');
        Route::get('/search-students', [BookIssueController::class, 'searchStudents'])->name('search-students');
    });
    
    // Staff Management (Phase 6 / Phase B Week 3-5)
    Route::resource('staff', StaffController::class);
    Route::get('/staff-search', [StaffController::class, 'search'])->name('staff.search');
    
    // Staff Departments & Positions
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    
    // Leave Types Management
    Route::resource('leave-types', LeaveTypeController::class);
    Route::post('/leave-types/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('leave-types.toggle-status');
    
    // Staff Leave Applications
    Route::resource('staff-leaves', StaffLeaveController::class);
    Route::post('/staff-leaves/{staffLeave}/approve', [StaffLeaveController::class, 'approve'])->name('staff-leaves.approve');
    Route::post('/staff-leaves/{staffLeave}/reject', [StaffLeaveController::class, 'reject'])->name('staff-leaves.reject');
    
    // ⚠️ SALARY MANAGEMENT - DISABLED (staff_salaries table doesn't exist)
    // To enable: Execute database/schema/missing-tables.sql
    // Route::prefix('salaries')->name('salaries.')->group(function () {
    //     Route::get('/', [SalaryController::class, 'index'])->name('index');
    //     Route::get('/process', [SalaryController::class, 'process'])->name('process');
    //     Route::post('/store', [SalaryController::class, 'store'])->name('store');
    //     Route::post('/generate-bulk', [SalaryController::class, 'generateBulk'])->name('generate-bulk');
    //     Route::post('/{salary}/mark-paid', [SalaryController::class, 'markAsPaid'])->name('mark-paid');
    //     Route::get('/{salary}/slip', [SalaryController::class, 'slip'])->name('slip');
    //     Route::get('/staff/{staff}/history', [SalaryController::class, 'history'])->name('staff-history');
    // });
    
    // Examination Module (Phase 7 / Phase B Week 6-8)
    Route::prefix('exams')->name('exams.')->group(function () {
        // Exam Management
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
        
        // Subject Assignment
        Route::get('/{exam}/subjects', [ExamController::class, 'assignSubjects'])->name('subjects');
        Route::post('/{exam}/subjects', [ExamController::class, 'storeSubjects'])->name('subjects.store');
        
        // Timetable
        Route::get('/{exam}/timetable', [ExamController::class, 'timetable'])->name('timetable');
        
        // Publish/Unpublish - DISABLED (is_published column doesn't exist)
        // Route::post('/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('toggle-publish');
    });
    
    // Mark Entry
    Route::prefix('marks')->name('marks.')->group(function () {
        Route::get('/', [MarkController::class, 'index'])->name('index');
        Route::get('/entry', [MarkController::class, 'entryForm'])->name('entry');
        Route::post('/store', [MarkController::class, 'store'])->name('store');
        Route::get('/student', [MarkController::class, 'studentMarks'])->name('student');
        Route::get('/subject/{examSubject}', [MarkController::class, 'subjectMarks'])->name('subject');
        Route::get('/search-students', [MarkController::class, 'searchStudents'])->name('search-students');
    });
    
    // ⚠️ RESULTS MODULE - DISABLED (results table doesn't exist)
    // To enable: Execute database/schema/missing-tables.sql
    // Route::prefix('results')->name('results.')->group(function () {
    //     Route::get('/', [ResultController::class, 'index'])->name('index');
    //     Route::get('/generate', [ResultController::class, 'generateForm'])->name('generate');
    //     Route::post('/generate', [ResultController::class, 'generate'])->name('generate.store');
    //     Route::get('/{result}', [ResultController::class, 'view'])->name('view');
    //     Route::get('/class/results', [ResultController::class, 'classResults'])->name('class');
    //     Route::post('/{result}/toggle-publish', [ResultController::class, 'togglePublish'])->name('toggle-publish');
    // });
    
    // Staff Attendance Module (Priority - Staff attendance first)
    Route::prefix('staff-attendance')->name('staff-attendance.')->group(function () {
        // Staff Attendance Dashboard
        Route::get('/', [StaffAttendanceController::class, 'index'])->name('index');
        
        // Mark Staff Attendance
        Route::get('/register', [StaffAttendanceController::class, 'register'])->name('register');
        Route::post('/store', [StaffAttendanceController::class, 'store'])->name('store');
        
        // Edit Staff Attendance
        Route::get('/edit', [StaffAttendanceController::class, 'edit'])->name('edit');
        Route::put('/update', [StaffAttendanceController::class, 'update'])->name('update');
        
        // View Staff Attendance
        Route::get('/staff', [StaffAttendanceController::class, 'staffAttendance'])->name('staff');
        Route::get('/department', [StaffAttendanceController::class, 'departmentAttendance'])->name('department');
        
        // Search
        Route::get('/search-staff', [StaffAttendanceController::class, 'searchStaff'])->name('search-staff');
    });
    
    // Student Attendance Module (Phase C)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Attendance Dashboard
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        
        // Mark Attendance
        Route::get('/register', [AttendanceController::class, 'register'])->name('register');
        Route::post('/store', [AttendanceController::class, 'store'])->name('store');
        
        // Edit Attendance
        Route::get('/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/update', [AttendanceController::class, 'update'])->name('update');
        
        // View Attendance
        Route::get('/student', [AttendanceController::class, 'studentAttendance'])->name('student');
        Route::get('/class', [AttendanceController::class, 'classAttendance'])->name('class');
        
        // Search
        Route::get('/search-students', [AttendanceController::class, 'searchStudents'])->name('search-students');
    });
    
    // Attendance Reports
    Route::prefix('reports/attendance')->name('reports.attendance.')->group(function () {
        Route::get('/', [AttendanceReportController::class, 'index'])->name('index');
        Route::post('/generate', [AttendanceReportController::class, 'generate'])->name('generate');
        Route::get('/student', [AttendanceReportController::class, 'studentReport'])->name('student');
        Route::get('/class', [AttendanceReportController::class, 'classReport'])->name('class');
        Route::get('/monthly', [AttendanceReportController::class, 'monthlyReport'])->name('monthly');
        Route::get('/daterange', [AttendanceReportController::class, 'dateRangeReport'])->name('daterange');
    });
    
    // Account Categories (Phase D)
    Route::resource('categories', AccountCategoryController::class);
    Route::post('/categories/{category}/toggle-status', [AccountCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Income Management
    Route::resource('income', IncomeController::class);
    
    // Expense Management
    Route::resource('expenses', ExpenseController::class);
    
    // Account Reports
    Route::prefix('reports/accounts')->name('reports.accounts.')->group(function () {
        Route::get('/', [AccountReportController::class, 'index'])->name('index');
        Route::get('/summary', [AccountReportController::class, 'summary'])->name('summary');
        Route::get('/details', [AccountReportController::class, 'details'])->name('details');
        Route::get('/export-csv', [AccountReportController::class, 'exportCsv'])->name('export-csv');
    });
    
    // Transport Module (Basic placeholder until full implementation)
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/', function() {
            return view('transport.index');
        })->name('index');
    });
    
    // Settings Module - RBAC Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        
        // Role Management
        Route::resource('roles', RoleController::class);
        
        // Permission Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    });
});
