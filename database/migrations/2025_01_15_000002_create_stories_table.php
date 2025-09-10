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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('content');
            $table->integer('word_count')->default(0);
            $table->integer('reading_time_minutes')->default(0); // Calculated reading time
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('status')->default(1); // 1=pending, 2=approved, 3=in_review, 4=rejected
            $table->integer('report_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('slug');
            $table->index('created_at');
        });

        // Story connections table (for connecting related stories)
        Schema::create('story_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('connected_story_id')->constrained('stories')->onDelete('cascade');
            $table->string('connection_type')->default('related'); // 'related', 'sequel', 'prequel', 'spin_off'
            $table->text('description')->nullable(); // Optional description of the connection
            $table->timestamps();
            
            // Ensure unique connections and prevent self-connections
            $table->unique(['story_id', 'connected_story_id']);
            $table->index(['story_id', 'connection_type']);
            $table->index(['connected_story_id', 'connection_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_connections');
        Schema::dropIfExists('stories');
    }
};