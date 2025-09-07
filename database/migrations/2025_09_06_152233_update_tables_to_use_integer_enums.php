<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify columns without dropping them (avoids foreign key issues)
        if (DB::getDriverName() === 'mysql') {
            // MySQL syntax
            DB::statement('ALTER TABLE tasks MODIFY COLUMN target_user_type INT DEFAULT 4');
            DB::statement('ALTER TABLE tasks MODIFY COLUMN status INT DEFAULT 1');
            
            DB::statement('ALTER TABLE task_rewards MODIFY COLUMN target_user_type INT DEFAULT 4');
            DB::statement('ALTER TABLE task_rewards MODIFY COLUMN status INT DEFAULT 1');
            
            DB::statement('ALTER TABLE task_punishments MODIFY COLUMN target_user_type INT DEFAULT 4');
            DB::statement('ALTER TABLE task_punishments MODIFY COLUMN status INT DEFAULT 1');
            
            DB::statement('ALTER TABLE user_assigned_tasks MODIFY COLUMN status INT DEFAULT 1');
        } else {
            // SQLite syntax - recreate tables
            $this->recreateTableForSQLite('tasks');
            $this->recreateTableForSQLite('task_rewards');
            $this->recreateTableForSQLite('task_punishments');
            $this->recreateTableForSQLite('user_assigned_tasks');
        }
    }

    private function recreateTableForSQLite(string $tableName): void
    {
        // For SQLite, we need to recreate the table
        // This is a simplified approach - in production you'd want more robust handling
        Schema::dropIfExists($tableName . '_backup');
        
        // Create backup
        DB::statement("CREATE TABLE {$tableName}_backup AS SELECT * FROM {$tableName}");
        
        // Drop original
        Schema::dropIfExists($tableName);
        
        // Recreate with integer columns
        if ($tableName === 'tasks') {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->integer('difficulty_level');
                $table->integer('target_user_type')->default(4); // 'any'
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('status')->default(1); // 'pending'
                $table->integer('view_count')->default(0);
                $table->boolean('is_premium')->default(false);
                $table->timestamps();
                
                $table->index(['status', 'target_user_type']);
                $table->index(['user_id', 'status']);
            });
        } elseif ($tableName === 'task_rewards') {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->integer('difficulty_level');
                $table->integer('target_user_type')->default(4); // 'any'
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('status')->default(1); // 'pending'
                $table->integer('view_count')->default(0);
                $table->boolean('is_premium')->default(false);
                $table->timestamps();
                
                $table->index(['status', 'target_user_type']);
                $table->index(['user_id', 'status']);
            });
        } elseif ($tableName === 'task_punishments') {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->integer('difficulty_level');
                $table->integer('target_user_type')->default(4); // 'any'
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('status')->default(1); // 'pending'
                $table->integer('view_count')->default(0);
                $table->boolean('is_premium')->default(false);
                $table->timestamps();
                
                $table->index(['status', 'target_user_type']);
                $table->index(['user_id', 'status']);
            });
        } elseif ($tableName === 'user_assigned_tasks') {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('task_id')->constrained()->onDelete('cascade');
                $table->integer('status')->default(1); // 'assigned'
                $table->string('outcome_type')->nullable(); // 'reward' or 'punishment'
                $table->unsignedBigInteger('outcome_id')->nullable(); // ID of reward or punishment received
                $table->foreignId('potential_reward_id')->nullable()->constrained('task_rewards')->onDelete('set null');
                $table->foreignId('potential_punishment_id')->nullable()->constrained('task_punishments')->onDelete('set null');
                $table->timestamp('assigned_at');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index(['task_id', 'status']);
                $table->index(['user_id', 'assigned_at']);
                $table->index(['outcome_type', 'outcome_id']);
                $table->unique(['user_id', 'task_id']);
            });
        }
        
        // Copy data back with proper column mapping
        if ($tableName === 'tasks') {
            DB::statement("INSERT INTO {$tableName} (id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at) 
                SELECT id, title, description, difficulty_level,
                       CASE target_user_type 
                           WHEN 'male' THEN 1 
                           WHEN 'female' THEN 2 
                           WHEN 'couple' THEN 3 
                           WHEN 'any' THEN 4 
                           ELSE 4 
                       END,
                       user_id,
                       CASE status 
                           WHEN 'pending' THEN 1 
                           WHEN 'approved' THEN 2 
                           WHEN 'in_review' THEN 3 
                           WHEN 'rejected' THEN 4 
                           ELSE 1 
                       END,
                       view_count, is_premium, created_at, updated_at 
                FROM {$tableName}_backup");
        } elseif ($tableName === 'task_rewards') {
            DB::statement("INSERT INTO {$tableName} (id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at) 
                SELECT id, title, description, difficulty_level,
                       CASE target_user_type 
                           WHEN 'male' THEN 1 
                           WHEN 'female' THEN 2 
                           WHEN 'couple' THEN 3 
                           WHEN 'any' THEN 4 
                           ELSE 4 
                       END,
                       user_id,
                       CASE status 
                           WHEN 'pending' THEN 1 
                           WHEN 'approved' THEN 2 
                           WHEN 'in_review' THEN 3 
                           WHEN 'rejected' THEN 4 
                           ELSE 1 
                       END,
                       view_count, is_premium, created_at, updated_at 
                FROM {$tableName}_backup");
        } elseif ($tableName === 'task_punishments') {
            DB::statement("INSERT INTO {$tableName} (id, title, description, difficulty_level, target_user_type, user_id, status, view_count, is_premium, created_at, updated_at) 
                SELECT id, title, description, difficulty_level,
                       CASE target_user_type 
                           WHEN 'male' THEN 1 
                           WHEN 'female' THEN 2 
                           WHEN 'couple' THEN 3 
                           WHEN 'any' THEN 4 
                           ELSE 4 
                       END,
                       user_id,
                       CASE status 
                           WHEN 'pending' THEN 1 
                           WHEN 'approved' THEN 2 
                           WHEN 'in_review' THEN 3 
                           WHEN 'rejected' THEN 4 
                           ELSE 1 
                       END,
                       view_count, is_premium, created_at, updated_at 
                FROM {$tableName}_backup");
        } elseif ($tableName === 'user_assigned_tasks') {
            DB::statement("INSERT INTO {$tableName} (id, user_id, task_id, status, outcome_type, outcome_id, potential_reward_id, potential_punishment_id, assigned_at, completed_at, created_at, updated_at) 
                SELECT id, user_id, task_id,
                       CASE status 
                           WHEN 'assigned' THEN 1 
                           WHEN 'completed' THEN 2 
                           WHEN 'failed' THEN 3 
                           ELSE 1 
                       END,
                       outcome_type, outcome_id, potential_reward_id, potential_punishment_id,
                       assigned_at, completed_at, created_at, updated_at 
                FROM {$tableName}_backup");
        }
        
        Schema::dropIfExists($tableName . '_backup');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to enum columns
        if (DB::getDriverName() === 'mysql') {
            // MySQL syntax
            DB::statement('ALTER TABLE tasks MODIFY COLUMN target_user_type ENUM("male", "female", "couple", "any") DEFAULT "any"');
            DB::statement('ALTER TABLE tasks MODIFY COLUMN status ENUM("pending", "approved", "in_review", "rejected") DEFAULT "pending"');
            
            DB::statement('ALTER TABLE task_rewards MODIFY COLUMN target_user_type ENUM("male", "female", "couple", "any") DEFAULT "any"');
            DB::statement('ALTER TABLE task_rewards MODIFY COLUMN status ENUM("pending", "approved", "in_review", "rejected") DEFAULT "pending"');
            
            DB::statement('ALTER TABLE task_punishments MODIFY COLUMN target_user_type ENUM("male", "female", "couple", "any") DEFAULT "any"');
            DB::statement('ALTER TABLE task_punishments MODIFY COLUMN status ENUM("pending", "approved", "in_review", "rejected") DEFAULT "pending"');
            
            DB::statement('ALTER TABLE user_assigned_tasks MODIFY COLUMN status ENUM("assigned", "completed", "failed") DEFAULT "assigned"');
        } else {
            // For SQLite, we'd need to recreate tables again - this is complex
            // In practice, you'd want to handle this more carefully
            throw new \Exception('Rollback not supported for SQLite in this migration');
        }
    }
};