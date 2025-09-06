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
        // Update tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('target_user_type')->default(4); // Default to 'any'
            $table->integer('status')->default(1); // Default to 'pending'
        });

        // Update task_rewards table
        Schema::table('task_rewards', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('task_rewards', function (Blueprint $table) {
            $table->integer('target_user_type')->default(4); // Default to 'any'
            $table->integer('status')->default(1); // Default to 'pending'
        });

        // Update task_punishments table
        Schema::table('task_punishments', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('task_punishments', function (Blueprint $table) {
            $table->integer('target_user_type')->default(4); // Default to 'any'
            $table->integer('status')->default(1); // Default to 'pending'
        });

        // Update user_assigned_tasks table
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            $table->integer('status')->default(1); // Default to 'assigned'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('target_user_type', ['male', 'female', 'couple', 'any']);
            $table->enum('status', ['pending', 'approved', 'in_review', 'rejected'])->default('pending');
        });

        // Revert task_rewards table
        Schema::table('task_rewards', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('task_rewards', function (Blueprint $table) {
            $table->enum('target_user_type', ['male', 'female', 'couple', 'any']);
            $table->enum('status', ['pending', 'approved', 'in_review', 'rejected'])->default('pending');
        });

        // Revert task_punishments table
        Schema::table('task_punishments', function (Blueprint $table) {
            $table->dropColumn(['target_user_type', 'status']);
        });
        
        Schema::table('task_punishments', function (Blueprint $table) {
            $table->enum('target_user_type', ['male', 'female', 'couple', 'any']);
            $table->enum('status', ['pending', 'approved', 'in_review', 'rejected'])->default('pending');
        });

        // Revert user_assigned_tasks table
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            $table->enum('status', ['assigned', 'completed', 'failed'])->default('assigned');
        });
    }
};