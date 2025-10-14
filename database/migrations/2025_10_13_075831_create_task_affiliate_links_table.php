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
        Schema::create('task_affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('affiliate_link_id')->constrained()->onDelete('cascade');
            $table->string('link_text')->nullable(); // Custom text for the link (e.g., "Buy this toy")
            $table->text('description')->nullable(); // Optional description of why this link is relevant
            $table->integer('sort_order')->default(0); // For ordering multiple links
            $table->boolean('is_primary')->default(false); // Whether this is the main recommended link
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['task_id', 'affiliate_link_id']);
            
            // Indexes for performance
            $table->index(['task_id', 'sort_order']);
            $table->index(['task_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_affiliate_links');
    }
};