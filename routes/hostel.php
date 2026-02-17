<?php

/**
 * Hostel Management Routes
 * 
 * All routes for the Hostel Management Module
 */

use App\Http\Controllers\HostelController;
use App\Http\Controllers\HostelRoomController;
use App\Http\Controllers\HostelStudentAllocationController;
use App\Http\Controllers\HostelWardenController;
use App\Http\Controllers\HostelImprestWalletController;
use App\Http\Controllers\HostelExpenseController;
use Illuminate\Support\Facades\Route;

Route::prefix('hostel')->name('hostel.')->group(function () {
    
    // Dashboard
    Route::get('/', [HostelController::class, 'dashboard'])->name('dashboard');

    // Hostel Management
    Route::resource('hostels', HostelController::class)->except(['show'])->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'destroy' => 'destroy',
    ]);
    Route::get('hostels/{hostel}', [HostelController::class, 'show'])->name('show');

    // Room Management
    Route::resource('rooms', HostelRoomController::class)->names([
        'index' => 'rooms.index',
        'create' => 'rooms.create',
        'store' => 'rooms.store',
        'show' => 'rooms.show',
        'edit' => 'rooms.edit',
        'update' => 'rooms.update',
        'destroy' => 'rooms.destroy',
    ]);

    // Student Allocation (Check-in/Check-out)
    Route::prefix('allocations')->name('allocations.')->group(function () {
        Route::get('/', [HostelStudentAllocationController::class, 'index'])->name('index');
        Route::get('create', [HostelStudentAllocationController::class, 'create'])->name('create');
        Route::post('/', [HostelStudentAllocationController::class, 'store'])->name('store');
        Route::get('{allocation}', [HostelStudentAllocationController::class, 'show'])->name('show');
        Route::get('{allocation}/checkout', [HostelStudentAllocationController::class, 'checkoutForm'])->name('checkout.form');
        Route::post('{allocation}/checkout', [HostelStudentAllocationController::class, 'checkout'])->name('checkout');
        Route::post('{allocation}/cancel', [HostelStudentAllocationController::class, 'cancel'])->name('cancel');
    });

    // Warden Management
    Route::resource('wardens', HostelWardenController::class)->names([
        'index' => 'wardens.index',
        'create' => 'wardens.create',
        'store' => 'wardens.store',
        'show' => 'wardens.show',
        'edit' => 'wardens.edit',
        'update' => 'wardens.update',
        'destroy' => 'wardens.destroy',
    ]);

    // Imprest Wallet Management
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [HostelImprestWalletController::class, 'index'])->name('index');
        Route::get('create', [HostelImprestWalletController::class, 'create'])->name('create');
        Route::post('/', [HostelImprestWalletController::class, 'store'])->name('store');
        Route::get('{wallet}', [HostelImprestWalletController::class, 'show'])->name('show');
        Route::get('{wallet}/credit', [HostelImprestWalletController::class, 'creditForm'])->name('credit.form');
        Route::post('{wallet}/credit', [HostelImprestWalletController::class, 'credit'])->name('credit');
        Route::get('{wallet}/statement', [HostelImprestWalletController::class, 'statement'])->name('statement');
        Route::post('{wallet}/toggle', [HostelImprestWalletController::class, 'toggleActive'])->name('toggle');
    });

    // Expense Management
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [HostelExpenseController::class, 'index'])->name('index');
        Route::get('create', [HostelExpenseController::class, 'create'])->name('create');
        Route::post('/', [HostelExpenseController::class, 'store'])->name('store');
        Route::get('pending', [HostelExpenseController::class, 'pendingApprovals'])->name('pending');
        Route::get('{expense}', [HostelExpenseController::class, 'show'])->name('show');
        Route::get('{expense}/approve', [HostelExpenseController::class, 'approveForm'])->name('approve.form');
        Route::post('{expense}/approve', [HostelExpenseController::class, 'approve'])->name('approve');
        Route::get('{expense}/reject', [HostelExpenseController::class, 'rejectForm'])->name('reject.form');
        Route::post('{expense}/reject', [HostelExpenseController::class, 'reject'])->name('reject');
    });
});
