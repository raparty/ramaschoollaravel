<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\FeePackageController;
use App\Http\Controllers\FeeController;

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
    Route::resource('fees', FeeController::class);
    Route::post('/fees/{fee}/collect', [FeeController::class, 'collectFee'])->name('fees.collect');
    Route::get('/fees/receipt/{fee}', [FeeController::class, 'printReceipt'])->name('fees.receipt');
    Route::get('/fees/pending', [FeeController::class, 'pendingFees'])->name('fees.pending');
    
    // TODO: Add Library routes (Phase 5)
    // TODO: Add Staff routes (Phase 6)
    // TODO: Add Exam routes (Phase 7)
    // TODO: Add Transport routes (Phase 8)
    // TODO: Add Accounts routes (Phase 9)
    // TODO: Add Attendance routes (Phase 10)
    // TODO: Add Classes/Subjects/Sections routes (Phase 11)
});
