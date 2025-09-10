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
            $table->boolean('has_completion_image')->default(false);
            $table->text('completion_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_assigned_tasks', function (Blueprint $table) {
            $table->dropColumn(['has_completion_image', 'completion_note']);
        });
    }
};
