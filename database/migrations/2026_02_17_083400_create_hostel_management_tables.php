<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates all tables for the Hostel Management Module
     * 
     * IMPORTANT: This migration requires that the admissions table exists with 
     * 'id' column as INT (not BIGINT) to match the production schema.
     * 
     * All foreign keys to admissions.id use integer() to match the 
     * column type. If you encounter "incompatible column type" errors:
     * 
     * 1. Ensure the core_tables migration (2026_02_14_072514) has been run first
     * 2. Verify admissions.id is INT (may be signed or unsigned depending on your setup)
     * 3. Drop any partially created hostel tables using: 
     *    php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
     * 4. Pull the latest code and re-run this migration
     */
    public function up(): void
    {
        // Verify that the admissions table exists before proceeding
        if (!Schema::hasTable('admissions')) {
            throw new \RuntimeException(
                'The admissions table must exist before running this migration. ' .
                'Please run the core tables migration (2026_02_14_072514_create_core_tables.php) first.'
            );
        }

        // Verify admissions.id is INT (not BIGINT)
        // Production database uses INT (signed), not INT UNSIGNED
        $admissionsIdType = DB::selectOne(
            "SELECT DATA_TYPE, COLUMN_TYPE 
             FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_SCHEMA = DATABASE() 
             AND TABLE_NAME = 'admissions' 
             AND COLUMN_NAME = 'id'"
        );
        
        if ($admissionsIdType && stripos($admissionsIdType->COLUMN_TYPE, 'bigint') !== false) {
            throw new \RuntimeException(
                'The admissions.id column must be INT (not BIGINT). ' .
                'The current type is: ' . $admissionsIdType->COLUMN_TYPE . '. ' .
                'This migration uses integer() for foreign keys to match the expected INT type. ' .
                'Please check the core tables migration and ensure it uses the correct type for the admissions table.'
            );
        }

        // 1. Hostels table - Main hostel entity
        if (!Schema::hasTable('hostels')) {
            Schema::create('hostels', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->enum('type', ['Boys', 'Girls', 'Junior', 'Senior'])->default('Boys');
                $table->integer('total_capacity')->default(0);
                $table->string('address')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('type');
                $table->index('is_active');
            });
        }

        // 2. Hostel Blocks table
        if (!Schema::hasTable('hostel_blocks')) {
            Schema::create('hostel_blocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->string('name', 50);
                $table->integer('total_floors')->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('hostel_id');
            });
        }

        // 3. Hostel Floors table
        if (!Schema::hasTable('hostel_floors')) {
            Schema::create('hostel_floors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('block_id')->constrained('hostel_blocks')->onDelete('cascade');
                $table->integer('floor_number');
                $table->string('name', 50);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('block_id');
                $table->index('floor_number');
            });
        }

        // 4. Hostel Rooms table
        if (!Schema::hasTable('hostel_rooms')) {
            Schema::create('hostel_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('floor_id')->constrained('hostel_floors')->onDelete('cascade');
                $table->string('room_number', 50);
                $table->enum('room_type', ['Single', 'Double', 'Triple', 'Dormitory'])->default('Double');
                $table->integer('max_strength')->default(1);
                $table->decimal('area_sqft', 10, 2)->nullable();
                $table->boolean('has_attached_bathroom')->default(false);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('floor_id');
                $table->index('room_number');
                $table->index('room_type');
            });
        }

        // 5. Hostel Beds table
        if (!Schema::hasTable('hostel_beds')) {
            Schema::create('hostel_beds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
                $table->string('bed_number', 50);
                $table->string('qr_code', 100)->unique();
                $table->enum('condition_status', ['Good', 'Damaged', 'Under Repair'])->default('Good');
                $table->text('notes')->nullable();
                $table->boolean('is_occupied')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('room_id');
                $table->index('qr_code');
                $table->index('is_occupied');
                $table->index('condition_status');
            });
        }

        // 6. Hostel Lockers table
        if (!Schema::hasTable('hostel_lockers')) {
            Schema::create('hostel_lockers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
                $table->string('locker_number', 50);
                $table->string('qr_code', 100)->unique();
                $table->enum('condition_status', ['Good', 'Damaged', 'Under Repair'])->default('Good');
                $table->boolean('has_key')->default(true);
                $table->text('notes')->nullable();
                $table->boolean('is_assigned')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('room_id');
                $table->index('qr_code');
                $table->index('is_assigned');
            });
        }

        // 7. Hostel Furniture table
        if (!Schema::hasTable('hostel_furniture')) {
            Schema::create('hostel_furniture', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
                $table->string('asset_code', 100)->unique();
                $table->string('item_name', 100);
                $table->enum('furniture_type', ['Bed', 'Chair', 'Table', 'Cupboard', 'Fan', 'Light', 'Other'])->default('Other');
                $table->integer('quantity')->default(1);
                $table->enum('condition_status', ['Good', 'Damaged', 'Under Repair'])->default('Good');
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_value', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('room_id');
                $table->index('asset_code');
                $table->index('furniture_type');
            });
        }

        // 8. Hostel Student Allocations table
        if (!Schema::hasTable('hostel_student_allocations')) {
            Schema::create('hostel_student_allocations', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->foreignId('bed_id')->constrained('hostel_beds')->onDelete('restrict');
                $table->foreignId('locker_id')->nullable()->constrained('hostel_lockers')->onDelete('set null');
                $table->date('check_in_date');
                $table->date('check_out_date')->nullable();
                $table->enum('status', ['Active', 'Checked Out', 'Cancelled'])->default('Active');
                $table->text('check_in_remarks')->nullable();
                $table->text('check_out_remarks')->nullable();
                $table->string('receipt_number', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('bed_id');
                $table->index('status');
                $table->index('check_in_date');
                $table->index('check_out_date');
            });
        }

        // 9. Hostel Wardens table
        if (!Schema::hasTable('hostel_wardens')) {
            Schema::create('hostel_wardens', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('employee_code', 50)->unique();
                $table->string('email', 100)->nullable();
                $table->string('phone', 20);
                $table->string('address')->nullable();
                $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
                $table->date('date_of_joining')->nullable();
                $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Active');
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('employee_code');
                $table->index('status');
            });
        }

        // 10. Hostel Warden Assignments table
        if (!Schema::hasTable('hostel_warden_assignments')) {
            Schema::create('hostel_warden_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warden_id')->constrained('hostel_wardens')->onDelete('cascade');
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->date('assigned_from');
                $table->date('assigned_to')->nullable();
                $table->time('shift_start_time')->nullable();
                $table->time('shift_end_time')->nullable();
                $table->enum('shift_type', ['Morning', 'Evening', 'Night', 'Full Day'])->default('Full Day');
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('warden_id');
                $table->index('hostel_id');
                $table->index('assigned_from');
            });
        }

        // 11. Hostel Incidents table
        if (!Schema::hasTable('hostel_incidents')) {
            Schema::create('hostel_incidents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->foreignId('reported_by')->constrained('hostel_wardens')->onDelete('restrict');
                $table->unsignedInteger('student_id')->nullable();
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('set null');
                $table->string('title', 200);
                $table->text('description');
                $table->enum('severity', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
                $table->dateTime('incident_date');
                $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
                $table->text('resolution_notes')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('hostel_id');
                $table->index('reported_by');
                $table->index('status');
                $table->index('incident_date');
            });
        }

        // 12. Hostel Attendance table
        if (!Schema::hasTable('hostel_attendance')) {
            Schema::create('hostel_attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->date('attendance_date');
                $table->enum('status', ['Present', 'Absent', 'On Leave'])->default('Present');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('submitted_by')->nullable()->constrained('hostel_wardens')->onDelete('set null');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('hostel_id');
                $table->index('student_id');
                $table->index('attendance_date');
                $table->index('status');
                $table->unique(['student_id', 'attendance_date']);
            });
        }

        // 13. Hostel Complaints table
        if (!Schema::hasTable('hostel_complaints')) {
            Schema::create('hostel_complaints', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->string('subject', 200);
                $table->text('description');
                $table->enum('category', ['Maintenance', 'Food', 'Staff Behavior', 'Safety', 'Other'])->default('Other');
                $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
                $table->enum('status', ['Submitted', 'Under Review', 'Resolved', 'Rejected'])->default('Submitted');
                $table->text('response')->nullable();
                $table->foreignId('responded_by')->nullable()->constrained('hostel_wardens')->onDelete('set null');
                $table->dateTime('responded_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('hostel_id');
                $table->index('status');
                $table->index('category');
            });
        }

        // 14. Hostel Fee Structures table
        if (!Schema::hasTable('hostel_fee_structures')) {
            Schema::create('hostel_fee_structures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
                $table->string('fee_name', 100);
                $table->decimal('amount', 10, 2);
                $table->enum('fee_type', ['Monthly', 'Quarterly', 'Half Yearly', 'Yearly', 'One Time'])->default('Monthly');
                $table->enum('category', ['Accommodation', 'Food', 'Maintenance', 'Security Deposit', 'Other'])->default('Accommodation');
                $table->text('description')->nullable();
                $table->boolean('is_mandatory')->default(true);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('hostel_id');
                $table->index('fee_type');
                $table->index('category');
            });
        }

        // 15. Hostel Student Fees (Ledger) table
        if (!Schema::hasTable('hostel_student_fees')) {
            Schema::create('hostel_student_fees', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->foreignId('fee_structure_id')->constrained('hostel_fee_structures')->onDelete('restrict');
                $table->decimal('amount_due', 10, 2);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->decimal('amount_balance', 10, 2)->default(0);
                $table->date('due_date');
                $table->date('paid_date')->nullable();
                $table->enum('status', ['Pending', 'Partial', 'Paid', 'Overdue', 'Waived'])->default('Pending');
                $table->decimal('fine_amount', 10, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('fee_structure_id');
                $table->index('due_date');
                $table->index('status');
            });
        }

        // 16. Hostel Payments table
        if (!Schema::hasTable('hostel_payments')) {
            Schema::create('hostel_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->foreignId('student_fee_id')->nullable()->constrained('hostel_student_fees')->onDelete('set null');
                $table->string('receipt_number', 50)->unique();
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->enum('payment_mode', ['Cash', 'Cheque', 'Card', 'UPI', 'Net Banking', 'Other'])->default('Cash');
                $table->string('transaction_reference', 100)->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('receipt_number');
                $table->index('payment_date');
                $table->index('payment_mode');
            });
        }

        // 17. Hostel Security Deposits table
        if (!Schema::hasTable('hostel_security_deposits')) {
            Schema::create('hostel_security_deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->foreignId('allocation_id')->constrained('hostel_student_allocations')->onDelete('cascade');
                $table->decimal('deposit_amount', 10, 2);
                $table->date('deposit_date');
                $table->string('receipt_number', 50)->unique();
                $table->decimal('refund_amount', 10, 2)->default(0);
                $table->decimal('deduction_amount', 10, 2)->default(0);
                $table->date('refund_date')->nullable();
                $table->enum('status', ['Held', 'Partially Refunded', 'Fully Refunded'])->default('Held');
                $table->text('deduction_remarks')->nullable();
                $table->text('refund_remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('allocation_id');
                $table->index('status');
            });
        }

        // 18. Hostel Imprest Wallets (Student Wallet) table
        if (!Schema::hasTable('hostel_imprest_wallets')) {
            Schema::create('hostel_imprest_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->foreign('student_id')->references('id')->on('admissions')->onDelete('cascade');
                $table->decimal('opening_balance', 10, 2)->default(0);
                $table->decimal('current_balance', 10, 2)->default(0);
                $table->decimal('total_credited', 10, 2)->default(0);
                $table->decimal('total_debited', 10, 2)->default(0);
                $table->date('wallet_opened_date');
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('student_id');
                $table->index('is_active');
                $table->unique('student_id');
            });
        }

        // 19. Hostel Expense Categories table
        if (!Schema::hasTable('hostel_expense_categories')) {
            Schema::create('hostel_expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->boolean('requires_approval')->default(true);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('is_active');
            });
        }

        // 20. Hostel Expenses table
        if (!Schema::hasTable('hostel_expenses')) {
            Schema::create('hostel_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained('hostel_imprest_wallets')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('hostel_expense_categories')->onDelete('restrict');
                $table->decimal('amount', 10, 2);
                $table->date('expense_date');
                $table->text('description');
                $table->string('bill_number', 50)->nullable();
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->foreignId('submitted_by')->nullable()->constrained('hostel_wardens')->onDelete('set null');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                
                $table->index('wallet_id');
                $table->index('category_id');
                $table->index('expense_date');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_expenses');
        Schema::dropIfExists('hostel_expense_categories');
        Schema::dropIfExists('hostel_imprest_wallets');
        Schema::dropIfExists('hostel_security_deposits');
        Schema::dropIfExists('hostel_payments');
        Schema::dropIfExists('hostel_student_fees');
        Schema::dropIfExists('hostel_fee_structures');
        Schema::dropIfExists('hostel_complaints');
        Schema::dropIfExists('hostel_attendance');
        Schema::dropIfExists('hostel_incidents');
        Schema::dropIfExists('hostel_warden_assignments');
        Schema::dropIfExists('hostel_wardens');
        Schema::dropIfExists('hostel_student_allocations');
        Schema::dropIfExists('hostel_furniture');
        Schema::dropIfExists('hostel_lockers');
        Schema::dropIfExists('hostel_beds');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostel_floors');
        Schema::dropIfExists('hostel_blocks');
        Schema::dropIfExists('hostels');
    }
};
