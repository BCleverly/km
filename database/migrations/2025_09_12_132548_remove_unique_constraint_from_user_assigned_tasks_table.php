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
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            // Remove the unique constraint on user_id and task_id to allow task redoing
            $table->dropUnique(['user_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            // Re-add the unique constraint if rolling back
            $table->unique(['user_id', 'task_id']);
        });
    }
};
