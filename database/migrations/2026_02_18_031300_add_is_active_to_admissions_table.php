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
        // Check if the table exists and the column doesn't exist
        if (Schema::hasTable('admissions') && !Schema::hasColumn('admissions', 'is_active')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('past_school_info');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admissions') && Schema::hasColumn('admissions', 'is_active')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
