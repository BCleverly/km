<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filesystem\EncryptedDisk;
use App\Services\FileEncryptionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class EncryptedFilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FileEncryptionService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('encrypted', function ($app, $config) {
            $encryptionService = $app->make(FileEncryptionService::class);
            
            return new EncryptedDisk(
                $encryptionService,
                $config['root'] ?? storage_path('app/encrypted'),
                $config
            );
        });
    }
}