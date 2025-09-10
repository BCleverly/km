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
        // Add view_count to fantasies table
        Schema::table('fantasies', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('report_count');
        });

        // Add view_count to stories table
        Schema::table('stories', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('report_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fantasies', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
    }
};