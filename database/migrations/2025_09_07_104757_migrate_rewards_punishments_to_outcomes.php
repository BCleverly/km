<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if old tables exist before migrating data
        $hasTaskRewards = Schema::hasTable('task_rewards');
        $hasTaskPunishments = Schema::hasTable('task_punishments');
        $hasTaskRecommendedRewards = Schema::hasTable('task_recommended_rewards');
        $hasTaskRecommendedPunishments = Schema::hasTable('task_recommended_punishments');

        if ($hasTaskRewards) {
            // Migrate task_rewards to outcomes
            DB::statement("
                INSERT INTO outcomes (
                    id, title, description, difficulty_level, target_user_type, 
                    user_id, status, view_count, is_premium, intended_type, 
                    created_at, updated_at
                )
                SELECT 
                    id, title, description, difficulty_level, target_user_type,
                    user_id, status, view_count, is_premium, 'reward' as intended_type,
                    created_at, updated_at
                FROM task_rewards
            ");
        }

        if ($hasTaskPunishments) {
            // Migrate task_punishments to outcomes (with offset IDs to avoid conflicts)
            $maxRewardId = $hasTaskRewards ? (DB::table('task_rewards')->max('id') ?? 0) : 0;
            
            DB::statement("
                INSERT INTO outcomes (
                    id, title, description, difficulty_level, target_user_type,
                    user_id, status, view_count, is_premium, intended_type,
                    created_at, updated_at
                )
                SELECT 
                    id + ? as id, title, description, difficulty_level, target_user_type,
                    user_id, status, view_count, is_premium, 'punishment' as intended_type,
                    created_at, updated_at
                FROM task_punishments
            ", [$maxRewardId]);
        }

        // Update user_outcomes table to reference new outcomes (only if table exists and has data)
        if (Schema::hasTable('user_outcomes') && DB::table('user_outcomes')->exists()) {
            $maxRewardId = $hasTaskRewards ? (DB::table('task_rewards')->max('id') ?? 0) : 0;
            
            // First, update reward references
            DB::statement("
                UPDATE user_outcomes 
                SET outcome_id = outcome_id, outcome_type = 'App\\\\Models\\\\Tasks\\\\Outcome'
                WHERE outcome_type = 'App\\\\Models\\\\Tasks\\\\TaskReward'
            ");

            // Then update punishment references (with offset)
            DB::statement("
                UPDATE user_outcomes 
                SET outcome_id = outcome_id + ?, outcome_type = 'App\\\\Models\\\\Tasks\\\\Outcome'
                WHERE outcome_type = 'App\\\\Models\\\\Tasks\\\\TaskPunishment'
            ", [$maxRewardId]);
        }

        // Create new pivot table
        Schema::create('task_recommended_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('outcome_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['task_id', 'outcome_id']);
            $table->index(['task_id', 'sort_order']);
        });

        // Migrate pivot table data if old tables exist
        if ($hasTaskRecommendedRewards) {
            DB::statement("
                INSERT INTO task_recommended_outcomes (task_id, outcome_id, sort_order, created_at, updated_at)
                SELECT task_id, task_reward_id as outcome_id, sort_order, created_at, updated_at
                FROM task_recommended_rewards
            ");
        }

        if ($hasTaskRecommendedPunishments) {
            $maxRewardId = $hasTaskRewards ? (DB::table('task_rewards')->max('id') ?? 0) : 0;
            
            DB::statement("
                INSERT INTO task_recommended_outcomes (task_id, outcome_id, sort_order, created_at, updated_at)
                SELECT task_id, task_punishment_id + ? as outcome_id, sort_order, created_at, updated_at
                FROM task_recommended_punishments
            ", [$maxRewardId]);
        }

        // Drop foreign key constraints from user_assigned_tasks table first
        if (Schema::hasTable('user_assigned_tasks')) {
            Schema::table('user_assigned_tasks', function (Blueprint $table) {
                $table->dropForeign(['potential_reward_id']);
                $table->dropForeign(['potential_punishment_id']);
            });
        }

        // Add foreign key constraints back to user_assigned_tasks table for outcomes
        if (Schema::hasTable('user_assigned_tasks')) {
            Schema::table('user_assigned_tasks', function (Blueprint $table) {
                $table->foreign('potential_reward_id')->references('id')->on('outcomes')->onDelete('set null');
                $table->foreign('potential_punishment_id')->references('id')->on('outcomes')->onDelete('set null');
            });
        }

        // Drop old tables if they exist
        if ($hasTaskRecommendedRewards) {
            Schema::dropIfExists('task_recommended_rewards');
        }
        if ($hasTaskRecommendedPunishments) {
            Schema::dropIfExists('task_recommended_punishments');
        }
        if ($hasTaskRewards) {
            Schema::dropIfExists('task_rewards');
        }
        if ($hasTaskPunishments) {
            Schema::dropIfExists('task_punishments');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if outcomes table exists before attempting rollback
        if (!Schema::hasTable('outcomes')) {
            return; // Nothing to rollback if outcomes table doesn't exist
        }

        // This is a complex rollback - we'll recreate the old tables
        // but data loss is possible due to ID conflicts
        
        // Recreate task_rewards table
        Schema::create('task_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level')->default(1);
            $table->string('target_user_type')->default('any');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });

        // Recreate task_punishments table
        Schema::create('task_punishments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level')->default(1);
            $table->string('target_user_type')->default('any');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });

        // Migrate outcomes back to separate tables (only if outcomes table has data)
        if (DB::table('outcomes')->exists()) {
            DB::statement("
                INSERT INTO task_rewards (id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at)
                SELECT id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at
                FROM outcomes WHERE intended_type = 'reward'
            ");

            $maxRewardId = DB::table('outcomes')->where('intended_type', 'reward')->max('id') ?? 0;
            
            DB::statement("
                INSERT INTO task_punishments (id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at)
                SELECT id - ? as id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at
                FROM outcomes WHERE intended_type = 'punishment'
            ", [$maxRewardId]);
        }

        // Recreate old pivot tables
        Schema::create('task_recommended_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_reward_id')->constrained('task_rewards')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['task_id', 'task_reward_id']);
            $table->index(['task_id', 'sort_order']);
        });

        Schema::create('task_recommended_punishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_punishment_id')->constrained('task_punishments')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['task_id', 'task_punishment_id']);
            $table->index(['task_id', 'sort_order']);
        });

        // Migrate back the pivot data (only if new pivot table exists and has data)
        if (Schema::hasTable('task_recommended_outcomes') && DB::table('task_recommended_outcomes')->exists()) {
            DB::statement("
                INSERT INTO task_recommended_rewards (task_id, task_reward_id, sort_order, created_at, updated_at)
                SELECT task_id, outcome_id as task_reward_id, sort_order, created_at, updated_at
                FROM task_recommended_outcomes 
                WHERE outcome_id <= ?
            ", [$maxRewardId]);

            DB::statement("
                INSERT INTO task_recommended_punishments (task_id, task_punishment_id, sort_order, created_at, updated_at)
                SELECT task_id, outcome_id - ? as task_punishment_id, sort_order, created_at, updated_at
                FROM task_recommended_outcomes 
                WHERE outcome_id > ?
            ", [$maxRewardId, $maxRewardId]);
        }

        // Update user_outcomes back to old structure (only if table exists and has data)
        if (Schema::hasTable('user_outcomes') && DB::table('user_outcomes')->exists()) {
            DB::statement("
                UPDATE user_outcomes 
                SET outcome_type = 'App\\\\Models\\\\Tasks\\\\TaskReward'
                WHERE outcome_type = 'App\\\\Models\\\\Tasks\\\\Outcome' AND outcome_id <= ?
            ", [$maxRewardId]);

            DB::statement("
                UPDATE user_outcomes 
                SET outcome_id = outcome_id - ?, outcome_type = 'App\\\\Models\\\\Tasks\\\\TaskPunishment'
                WHERE outcome_type = 'App\\\\Models\\\\Tasks\\\\Outcome' AND outcome_id > ?
            ", [$maxRewardId, $maxRewardId]);
        }

        // Drop new tables
        Schema::dropIfExists('task_recommended_outcomes');
        Schema::dropIfExists('outcomes');
    }
};