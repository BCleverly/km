<?php

use App\ContentStatus;
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
        Schema::table('stories', function (Blueprint $table) {
            // Remove the premium feature
            $table->dropColumn('is_premium');
            
            // Add draft status - 0=draft, 1=pending, 2=approved, 3=in_review, 4=rejected
            $table->integer('status')->default(ContentStatus::Draft->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            // Add back the premium feature
            $table->boolean('is_premium')->default(false);
            
            // Revert status to original default
            $table->integer('status')->default(ContentStatus::Pending->value)->change();
        });
    }
};