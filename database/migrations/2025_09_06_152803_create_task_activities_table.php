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
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_assigned_task_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('activity_type'); // 'assigned', 'completed', 'failed', 'reward_received', 'punishment_received'
            $table->string('title'); // Human-readable activity title
            $table->text('description')->nullable(); // Detailed description
            $table->json('metadata')->nullable(); // Additional data (outcome details, etc.)
            $table->timestamp('activity_at'); // When the activity occurred
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'activity_at']);
            $table->index(['task_id', 'activity_at']);
            $table->index(['activity_type', 'activity_at']);
            $table->index('activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_activities');
    }
};
