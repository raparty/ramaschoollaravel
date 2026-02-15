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
        // Create transport_drivers table
        if (!Schema::hasTable('transport_drivers')) {
            Schema::create('transport_drivers', function (Blueprint $table) {
                $table->increments('driver_id');
                $table->string('driver_name', 100);
                $table->string('license_number', 50)->nullable();
                $table->string('aadhar_number', 20)->nullable();
                $table->string('contact_number', 20)->nullable();
                $table->text('address')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_drivers');
    }
};
