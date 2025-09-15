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
        Schema::create('couple_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // The dom/partner who assigned the task
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade'); // The sub/partner who receives the task
            $table->string('title');
            $table->text('description');
            $table->text('dom_message')->nullable(); // Personal message from the dom
            $table->integer('difficulty_level')->default(1); // 1-10 scale
            $table->integer('duration_hours')->default(24); // How long to complete
            $table->integer('status')->default(1); // 1=pending, 2=completed, 3=failed, 4=declined
            $table->foreignId('reward_id')->nullable()->constrained('outcomes')->onDelete('set null');
            $table->foreignId('punishment_id')->nullable()->constrained('outcomes')->onDelete('set null');
            $table->timestamp('assigned_at');
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable(); // Notes from the sub when completing
            $table->text('thank_you_message')->nullable(); // Thank you message from sub to dom
            $table->timestamp('thanked_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['assigned_by', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['assigned_to', 'assigned_at']);
            $table->index(['status', 'deadline_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_tasks');
    }
};
