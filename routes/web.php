<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\FeePackageController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\BookIssueController;

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
    
    // TODO: Add Staff routes (Phase 6)
    // TODO: Add Exam routes (Phase 7)
    // TODO: Add Transport routes (Phase 8)
    // TODO: Add Accounts routes (Phase 9)
    // TODO: Add Attendance routes (Phase 10)
    // TODO: Add Classes/Subjects/Sections routes (Phase 11)
});
