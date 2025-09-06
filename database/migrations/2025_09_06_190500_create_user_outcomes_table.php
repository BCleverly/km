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
    }
};
