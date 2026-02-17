<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates all core tables for the School ERP system
     */
    public function up(): void
    {
        // Schools table
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // Classes table
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('has_streams')->default(false);
            $table->timestamps();
        });

        // Sections table
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Streams table
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // Academic terms table
        if (!Schema::hasTable('terms')) {
            Schema::create('terms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Admissions/Students table
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('reg_no')->unique();
            $table->string('student_name');
            $table->string('student_pic')->nullable();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->foreignId('class_id')->constrained()->onDelete('restrict');
            $table->foreignId('section_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('stream_id')->nullable()->constrained()->onDelete('set null');
            $table->date('admission_date');
            $table->string('aadhaar_no')->nullable();
            $table->string('aadhaar_doc_path')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('address')->nullable();
            $table->text('past_school_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Fee Packages table
        Schema::create('fee_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Fee Terms table
        Schema::create('fee_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        // Student Fees table
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_package_id')->constrained()->onDelete('restrict');
            $table->foreignId('term_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->string('receipt_no')->nullable();
            $table->timestamps();
        });

        // Book Categories table
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Books table
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('book_categories')->onDelete('restrict');
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->unique()->nullable();
            $table->string('book_no')->unique();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('publisher')->nullable();
            $table->year('published_year')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });

        // Book Issues table (student_books in legacy)
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('restrict');
            $table->foreignId('book_id')->constrained()->onDelete('restrict');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue'])->default('issued');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->timestamps();
        });

        // Library Fines table
        Schema::create('library_fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('admission_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('fine_date');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'waived'])->default('pending');
            $table->timestamps();
        });

        // Staff Departments table
        Schema::create('staff_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Staff Positions table
        Schema::create('staff_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Staff Categories table
        Schema::create('staff_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Staff table
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('staff_departments')->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained('staff_positions')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('staff_categories')->onDelete('set null');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Transport Routes table
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('fee_amount', 10, 2);
            $table->timestamps();
        });

        // Transport Vehicles table
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->unique();
            $table->string('vehicle_type')->nullable();
            $table->foreignId('route_id')->nullable()->constrained('transport_routes')->onDelete('set null');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });

        // Student Transport table
        Schema::create('student_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('restrict');
            $table->foreignId('vehicle_id')->nullable()->constrained('transport_vehicles')->onDelete('set null');
            $table->decimal('monthly_fee', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Transport Fees table
        Schema::create('student_transport_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_id')->constrained('student_transport')->onDelete('cascade');
            $table->foreignId('term_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->string('receipt_no')->nullable();
            $table->timestamps();
        });

        // Exams table
        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('term_id')->constrained()->onDelete('cascade');
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamps();
            });
        }

        // Exam Subjects (Maximum Marks) table
        if (!Schema::hasTable('exam_subjects')) {
            Schema::create('exam_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->integer('theory_marks')->default(0);
                $table->integer('practical_marks')->default(0);
                $table->integer('pass_marks');
                $table->date('exam_date')->nullable();
                $table->time('exam_time')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->timestamps();
            });
        }

        // Student Marks table
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_subject_id')->constrained()->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2);
            $table->enum('grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'])->nullable();
            $table->boolean('is_absent')->default(false);
            $table->timestamps();
        });

        // Accounts - Income Categories
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Accounts - Expense Categories
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Accounts - Income
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('income_categories')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->date('income_date');
            $table->text('description')->nullable();
            $table->string('receipt_no')->nullable();
            $table->timestamps();
        });

        // Accounts - Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('expense_categories')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->text('description')->nullable();
            $table->string('voucher_no')->nullable();
            $table->timestamps();
        });

        // Attendance table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'half-day'])->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['admission_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('student_marks');
        Schema::dropIfExists('exam_subjects');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('student_transport_fees');
        Schema::dropIfExists('student_transport');
        Schema::dropIfExists('transport_vehicles');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('staff_categories');
        Schema::dropIfExists('staff_positions');
        Schema::dropIfExists('staff_departments');
        Schema::dropIfExists('library_fines');
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_terms');
        Schema::dropIfExists('fee_packages');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('schools');
    }
};
