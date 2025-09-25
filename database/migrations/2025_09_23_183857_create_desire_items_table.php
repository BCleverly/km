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
        Schema::create('desire_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('item_type')->comment('1=Fetish, 2=Fantasy, 3=Kink, 4=Toy, 5=Activity, 6=Roleplay');
            $table->foreignId('category_id')->nullable()->constrained('desire_categories')->onDelete('set null');
            $table->integer('target_user_type')->default(4)->comment('1=Male, 2=Female, 3=Couple, 4=Any');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('status')->default(1)->comment('1=Pending, 2=Approved, 3=InReview, 4=Rejected');
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->integer('difficulty_level')->default(1)->comment('1-10 scale');
            $table->json('tags')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'target_user_type']);
            $table->index(['user_id', 'status']);
            $table->index(['item_type', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['difficulty_level', 'status']);
            $table->index(['is_premium', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desire_items');
    }
};
