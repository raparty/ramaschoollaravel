<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('role_name', 100)->unique();
                $table->string('description', 255)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('module', 50)->comment('Module name (e.g., admission, fees, exam)');
                $table->string('submodule', 100)->nullable();
                $table->string('action', 50)->comment('Action type (view, add, edit, delete)');
                $table->string('description', 255)->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->unique(['module', 'action'], 'unique_permission');
            });
        }

        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('role', 100);
                $table->unsignedInteger('permission_id');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->unique(['role', 'permission_id'], 'unique_role_permission');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
