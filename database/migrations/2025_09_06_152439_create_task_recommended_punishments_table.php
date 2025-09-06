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
        Schema::create('task_recommended_punishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_punishment_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0); // For ordering recommendations
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['task_id', 'task_punishment_id']);
            
            // Indexes for performance
            $table->index(['task_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_recommended_punishments');
    }
};
