<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No schema changes needed - encryption metadata is stored in custom_properties JSON field
        // This migration is here for documentation and future schema changes if needed
        
        // Create the encrypted storage directory if it doesn't exist
        $encryptedPath = storage_path('app/encrypted');
        if (!is_dir($encryptedPath)) {
            mkdir($encryptedPath, 0755, true);
        }
    }

    public function down(): void
    {
        // No schema changes to revert
    }
};