<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ReencryptMediaFiles;
use App\Services\FileEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReencryptAllMediaFiles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'media:reencrypt 
                            {--batch-size=10 : Number of files to process in each batch}
                            {--force : Force re-encryption even if not needed}';

    /**
     * The console command description.
     */
    protected $description = 'Re-encrypt all media files with the current app key';

    /**
     * Execute the console command.
     */
    public function handle(FileEncryptionService $encryptionService): int
    {
        $currentKeyId = $encryptionService->getCurrentKeyId();
        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        $this->info("Starting re-encryption process with current key ID: {$currentKeyId}");

        // Get all media files that need re-encryption
        $query = Media::query();
        
        if (!$force) {
            $query->where(function ($q) use ($currentKeyId) {
                $q->whereNull('custom_properties->encryption_key_id')
                  ->orWhere('custom_properties->encryption_key_id', '!=', $currentKeyId);
            });
        }

        $totalFiles = $query->count();
        
        if ($totalFiles === 0) {
            $this->info('No files need re-encryption.');
            return 0;
        }

        $this->info("Found {$totalFiles} files that need re-encryption.");

        $progressBar = $this->output->createProgressBar($totalFiles);
        $progressBar->start();

        $processed = 0;
        $errors = 0;

        $query->chunk($batchSize, function ($mediaFiles) use ($currentKeyId, $progressBar, &$processed, &$errors) {
            foreach ($mediaFiles as $media) {
                try {
                    // Check if file exists on encrypted disk
                    if (!Storage::disk('encrypted')->exists($media->getPath())) {
                        $this->warn("File not found on encrypted disk: {$media->getPath()}");
                        $errors++;
                        $progressBar->advance();
                        continue;
                    }

                    // Dispatch re-encryption job
                    ReencryptMediaFiles::dispatch($media->id, $currentKeyId);
                    $processed++;
                    
                } catch (\Exception $e) {
                    $this->error("Error processing {$media->getPath()}: " . $e->getMessage());
                    $errors++;
                }
                
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();

        $this->info("Re-encryption jobs dispatched:");
        $this->info("- Total files: {$totalFiles}");
        $this->info("- Jobs dispatched: {$processed}");
        $this->info("- Errors: {$errors}");

        if ($errors > 0) {
            $this->warn("Some files had errors. Check the logs for details.");
        }

        $this->info("Re-encryption jobs are now running in the background.");
        $this->info("Monitor the queue to see progress: php artisan queue:work");

        return 0;
    }
}