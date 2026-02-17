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
        if (Schema::hasTable('student_books_details')) {
            Schema::table('student_books_details', function (Blueprint $table) {
                // Add due_date column after issue_date
                $table->date('due_date')->nullable()->after('issue_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_books_details')) {
            Schema::table('student_books_details', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }
    }
};
