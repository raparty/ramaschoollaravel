<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists (e.g., created manually in production)
        if (!Schema::hasTable('staff_leaves')) {
            Schema::create('staff_leaves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('restrict');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('days')->comment('Number of leave days');
                $table->text('reason');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('admin_remarks')->nullable();
                $table->timestamps();
                
                // Add index for staff_id without foreign key constraint
                // to avoid issues with staff table name inconsistency
                $table->index('staff_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leaves');
    }
};
