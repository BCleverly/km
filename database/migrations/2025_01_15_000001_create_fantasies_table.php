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
        Schema::create('fantasies', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->integer('word_count')->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('status')->default(1); // 1=pending, 2=approved, 3=in_review, 4=rejected
            $table->integer('view_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fantasies');
    }
};