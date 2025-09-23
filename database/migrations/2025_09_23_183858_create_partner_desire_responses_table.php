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
        Schema::create('partner_desire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('desire_item_id')->constrained('desire_items')->onDelete('cascade');
            $table->integer('response_type')->comment('1=No, 2=Maybe, 3=Yes');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'desire_item_id']);
            $table->index(['partner_id', 'desire_item_id']);
            $table->index(['desire_item_id', 'response_type']);
            $table->index(['user_id', 'response_type']);
            $table->index(['partner_id', 'response_type']);
            $table->index(['created_at']);

            // Unique constraint - one response per user per desire item
            $table->unique(['user_id', 'desire_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_desire_responses');
    }
};
