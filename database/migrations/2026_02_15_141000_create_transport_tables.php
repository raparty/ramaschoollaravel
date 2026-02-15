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
        // Create transport_add_route table
        if (!Schema::hasTable('transport_add_route')) {
            Schema::create('transport_add_route', function (Blueprint $table) {
                $table->increments('route_id');
                $table->string('route_name', 255);
                $table->decimal('cost', 10, 2)->default(0.00);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // Create transport_add_vechile table (note: keeping legacy spelling)
        if (!Schema::hasTable('transport_add_vechile')) {
            Schema::create('transport_add_vechile', function (Blueprint $table) {
                $table->increments('vechile_id');
                $table->string('vechile_no', 100);
                $table->text('route_id')->nullable(); // Stores comma-separated route IDs
                $table->integer('no_of_seats')->default(0);
            });
        }

        // Create transport_student_detail table
        if (!Schema::hasTable('transport_student_detail')) {
            Schema::create('transport_student_detail', function (Blueprint $table) {
                $table->increments('id');
                $table->string('registration_no', 50);
                $table->unsignedInteger('route_id');
                $table->unsignedInteger('vehicle_id'); // Note: Correct spelling in this table
                $table->integer('class_id')->nullable();
                $table->integer('stream_id')->nullable();
                $table->string('session', 50);
                
                // Add indexes for better performance
                $table->index('registration_no');
                $table->index('route_id');
                $table->index('vehicle_id');
                $table->index('session');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_student_detail');
        Schema::dropIfExists('transport_add_vechile');
        Schema::dropIfExists('transport_add_route');
    }
};
