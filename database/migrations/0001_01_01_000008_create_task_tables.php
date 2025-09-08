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
        // Tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level'); // 1-10 scale
            $table->integer('duration_time')->default(24); // Duration value
            $table->string('duration_type')->default('hours'); // Duration unit
            $table->integer('target_user_type')->default(4); // 1=male, 2=female, 3=couple, 4=any
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->integer('status')->default(1); // 1=pending, 2=approved, 3=in_review, 4=rejected
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'target_user_type']);
            $table->index(['user_id', 'status']);
        });

        // Outcomes table (consolidated rewards and punishments)
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level')->default(1);
            $table->integer('target_user_type')->default(4); // 1=male, 2=female, 3=couple, 4=any
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('status')->default(1); // 1=pending, 2=approved, 3=in_review, 4=rejected
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->string('intended_type'); // 'reward' or 'punishment'
            $table->timestamps();

            $table->index(['status', 'intended_type']);
            $table->index(['target_user_type', 'intended_type']);
            $table->index(['difficulty_level', 'intended_type']);
        });

        // User assigned tasks table
        Schema::create('user_assigned_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->integer('status')->default(1); // 1=assigned, 2=completed, 3=failed
            $table->string('outcome_type')->nullable(); // 'reward' or 'punishment'
            $table->unsignedBigInteger('outcome_id')->nullable(); // ID of reward or punishment received
            $table->foreignId('potential_reward_id')->nullable()->constrained('outcomes')->onDelete('set null');
            $table->foreignId('potential_punishment_id')->nullable()->constrained('outcomes')->onDelete('set null');
            $table->timestamp('assigned_at');
            $table->timestamp('deadline_at')->nullable();
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

        // Task recommended outcomes (pivot table)
        Schema::create('task_recommended_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('outcome_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0); // For ordering recommendations
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['task_id', 'outcome_id']);
            
            // Indexes for performance
            $table->index(['task_id', 'sort_order']);
        });

        // Task activities table
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

        // User outcomes table
        Schema::create('user_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('outcome'); // outcome_id, outcome_type (reward/punishment)
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_assigned_task_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('active'); // active, completed, expired, cancelled
            $table->text('notes')->nullable(); // User notes about the outcome
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // For time-limited outcomes
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['outcome_id', 'outcome_type']);
            $table->index('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_outcomes');
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_recommended_outcomes');
        Schema::dropIfExists('user_assigned_tasks');
        Schema::dropIfExists('outcomes');
        Schema::dropIfExists('tasks');
    }
};