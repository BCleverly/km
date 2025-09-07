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
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('difficulty_level')->default(1);
            $table->string('target_user_type')->default('any');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->integer('view_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->string('intended_type'); // 'reward' or 'punishment'
            $table->timestamps();

            $table->index(['status', 'intended_type']);
            $table->index(['target_user_type', 'intended_type']);
            $table->index(['difficulty_level', 'intended_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};