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
        Schema::create('user_assigned_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['assigned', 'completed', 'failed'])->default('assigned');
            $table->string('outcome_type')->nullable(); // 'reward' or 'punishment'
            $table->unsignedBigInteger('outcome_id')->nullable(); // ID of reward or punishment received
            $table->foreignId('potential_reward_id')->nullable()->constrained('task_rewards')->onDelete('set null');
            $table->foreignId('potential_punishment_id')->nullable()->constrained('task_punishments')->onDelete('set null');
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['task_id', 'status']);
            $table->index(['user_id', 'assigned_at']);
            $table->index(['outcome_type', 'outcome_id']);
            
            // Ensure user can't have multiple active tasks
            $table->unique(['user_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assigned_tasks');
    }
};
