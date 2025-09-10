<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\FileEncryptionService;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    /**
     * Get the encryption key ID used for this media file
     */
    public function getEncryptionKeyId(): ?string
    {
        return $this->getCustomProperty('encryption_key_id');
    }

    /**
     * Set the encryption key ID for this media file
     */
    public function setEncryptionKeyId(string $keyId): self
    {
        $this->setCustomProperty('encryption_key_id', $keyId);
        return $this;
    }

    /**
     * Get when this file was encrypted
     */
    public function getEncryptedAt(): ?\DateTimeInterface
    {
        $encryptedAt = $this->getCustomProperty('encrypted_at');
        
        if ($encryptedAt) {
            return is_string($encryptedAt) ? new \DateTime($encryptedAt) : $encryptedAt;
        }
        
        return null;
    }

    /**
     * Set when this file was encrypted
     */
    public function setEncryptedAt(\DateTimeInterface $date = null): self
    {
        $this->setCustomProperty('encrypted_at', $date ?? now());
        return $this;
    }

    /**
     * Check if this file needs re-encryption with the current key
     */
    public function needsReencryption(): bool
    {
        $currentKeyId = app(FileEncryptionService::class)->getCurrentKeyId();
        $fileKeyId = $this->getEncryptionKeyId();
        
        return $fileKeyId !== $currentKeyId;
    }

    /**
     * Get the file content from encrypted storage
     */
    public function getEncryptedContent(): string
    {
        if ($this->disk !== 'encrypted') {
            throw new \RuntimeException('Media file is not stored on encrypted disk');
        }

        return Storage::disk('encrypted')->get($this->getPath());
    }

    /**
     * Get the file content with encryption metadata
     */
    public function getEncryptedContentWithMetadata(): array
    {
        if ($this->disk !== 'encrypted') {
            throw new \RuntimeException('Media file is not stored on encrypted disk');
        }

        return Storage::disk('encrypted')->getWithMetadata($this->getPath());
    }

    /**
     * Store content to encrypted disk
     */
    public function storeToEncryptedDisk(string $content): self
    {
        $encryptionService = app(FileEncryptionService::class);
        
        // Store to encrypted disk
        Storage::disk('encrypted')->put($this->getPath(), $content);
        
        // Update metadata
        $this->setEncryptionKeyId($encryptionService->getCurrentKeyId());
        $this->setEncryptedAt();
        $this->disk = 'encrypted';
        $this->save();
        
        return $this;
    }

    /**
     * Move from current disk to encrypted disk
     */
    public function moveToEncryptedDisk(): self
    {
        if ($this->disk === 'encrypted') {
            return $this;
        }

        // Get content from current disk
        $content = Storage::disk($this->disk)->get($this->getPath());
        
        // Store to encrypted disk
        $this->storeToEncryptedDisk($content);
        
        // Delete from original disk
        Storage::disk($this->disk)->delete($this->getPath());
        
        return $this;
    }

    /**
     * Get the URL for the encrypted file (requires special handling)
     */
    public function getEncryptedUrl(): string
    {
        if ($this->disk !== 'encrypted') {
            return $this->getUrl();
        }

        // For encrypted files, we need to create a temporary URL
        // This would typically go through a controller that handles decryption
        return route('media.encrypted', ['media' => $this->id]);
    }

    /**
     * Check if the file is encrypted
     */
    public function isEncrypted(): bool
    {
        return $this->disk === 'encrypted' && $this->getEncryptionKeyId() !== null;
    }
}