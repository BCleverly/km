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
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Partner name (e.g., "Lovehoney", "Adam & Eve")
            $table->string('description')->nullable(); // Brief description of the partner
            $table->string('url'); // The affiliate URL
            $table->string('partner_type')->default('general'); // general, toys, clothing, books, etc.
            $table->string('commission_type')->default('percentage'); // percentage, fixed
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission rate (e.g., 5.50 for 5.5%)
            $table->decimal('commission_fixed', 8, 2)->nullable(); // Fixed commission amount
            $table->string('currency', 3)->default('USD'); // Currency code
            $table->boolean('is_active')->default(true); // Whether the link is currently active
            $table->boolean('is_premium')->default(false); // Whether this is a premium partner
            $table->string('tracking_id')->nullable(); // Partner tracking ID
            $table->text('notes')->nullable(); // Internal notes about the partner
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who created this link
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['is_active', 'partner_type']);
            $table->index(['is_premium', 'is_active']);
            $table->index('partner_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};