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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level'); // 1-10 scale
            $table->enum('target_user_type', ['male', 'female', 'couple', 'any']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->enum('status', ['pending', 'approved', 'in_review', 'rejected'])->default('pending');
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'target_user_type']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
