<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the fees_package table for managing school fee packages.
     * Adds timestamps (created_at, updated_at) to track when packages are created and modified.
     */
    public function up(): void
    {
        // Create fees_package table if it doesn't exist
        if (!Schema::hasTable('fees_package')) {
            Schema::create('fees_package', function (Blueprint $table) {
                $table->id();
                $table->string('package_name', 100);
                $table->decimal('total_amount', 10, 2);
                $table->timestamps(); // Add created_at and updated_at columns
                
                // Indexes for better query performance
                $table->index('package_name');
            });
        } else {
            // If table exists, add timestamps if they're missing
            if (!Schema::hasColumns('fees_package', ['created_at', 'updated_at'])) {
                Schema::table('fees_package', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('total_amount');
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_package');
    }
};
