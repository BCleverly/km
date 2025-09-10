<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\FileEncryptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReencryptMediaFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $mediaId,
        private string $oldKeyId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FileEncryptionService $encryptionService): void
    {
        try {
            $media = Media::find($this->mediaId);
            
            if (!$media) {
                Log::warning("Media file with ID {$this->mediaId} not found for re-encryption");
                return;
            }

            // Check if file exists on encrypted disk
            if (!Storage::disk('encrypted')->exists($media->getPath())) {
                Log::warning("Media file {$media->getPath()} not found on encrypted disk");
                return;
            }

            // Get file content with metadata
            $result = Storage::disk('encrypted')->getWithMetadata($media->getPath());
            
            // If file doesn't need re-encryption, skip
            if (!$result['needs_reencryption']) {
                Log::info("Media file {$media->getPath()} already encrypted with current key");
                return;
            }

            // Re-encrypt with current key
            $newEncryptedContent = $encryptionService->reencrypt($result['content']);
            
            // Write back to disk
            Storage::disk('encrypted')->put($media->getPath(), $newEncryptedContent);
            
            // Update media metadata
            $media->setCustomProperty('encryption_key_id', $encryptionService->getCurrentKeyId());
            $media->setCustomProperty('encrypted_at', now());
            $media->save();

            Log::info("Successfully re-encrypted media file {$media->getPath()} with new key");
            
        } catch (\Exception $e) {
            Log::error("Failed to re-encrypt media file {$this->mediaId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Re-encryption job failed for media ID {$this->mediaId}: " . $exception->getMessage());
    }
}