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
        // Add new fields to transport_add_vechile table
        if (Schema::hasTable('transport_add_vechile')) {
            Schema::table('transport_add_vechile', function (Blueprint $table) {
                // Add driver assignment
                if (!Schema::hasColumn('transport_add_vechile', 'driver_id')) {
                    $table->unsignedInteger('driver_id')->nullable()->after('no_of_seats');
                    $table->foreign('driver_id')->references('driver_id')->on('transport_drivers')->onDelete('set null');
                }
                
                // Add vehicle details
                if (!Schema::hasColumn('transport_add_vechile', 'insurance_number')) {
                    $table->string('insurance_number', 100)->nullable()->after('driver_id');
                }
                if (!Schema::hasColumn('transport_add_vechile', 'insurance_expiry')) {
                    $table->date('insurance_expiry')->nullable()->after('insurance_number');
                }
                if (!Schema::hasColumn('transport_add_vechile', 'permit_number')) {
                    $table->string('permit_number', 100)->nullable()->after('insurance_expiry');
                }
                if (!Schema::hasColumn('transport_add_vechile', 'permit_expiry')) {
                    $table->date('permit_expiry')->nullable()->after('permit_number');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transport_add_vechile')) {
            Schema::table('transport_add_vechile', function (Blueprint $table) {
                // Drop foreign key first
                if (Schema::hasColumn('transport_add_vechile', 'driver_id')) {
                    $table->dropForeign(['driver_id']);
                    $table->dropColumn('driver_id');
                }
                
                // Drop other columns
                $columns = ['insurance_number', 'insurance_expiry', 'permit_number', 'permit_expiry'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('transport_add_vechile', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
