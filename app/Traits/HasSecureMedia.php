<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\SecureImageService;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasSecureMedia
{
    /**
     * Add secure image to media collection
     */
    public function addSecureImage(UploadedFile $file, string $collectionName = 'default', array $customProperties = []): ?Media
    {
        $service = app(SecureImageService::class);
        
        return $service->addSecureImageToMedia($this, $file, $collectionName, $customProperties);
    }

    /**
     * Add secure image from content to media collection
     */
    public function addSecureImageFromContent(string $content, string $originalName, string $mimeType, string $collectionName = 'default', array $customProperties = []): ?Media
    {
        $service = app(SecureImageService::class);
        
        return $service->addSecureImageFromContentToMedia($this, $content, $originalName, $mimeType, $collectionName, $customProperties);
    }

    /**
     * Get secure image URL
     */
    public function getSecureImageUrl(string $collectionName, string $conversionName = ''): ?string
    {
        $media = $this->getFirstMedia($collectionName);
        
        if (!$media) {
            return null;
        }

        $service = app(SecureImageService::class);
        
        return $service->getSecureImageUrl($media, $conversionName);
    }

    /**
     * Get secure image URLs for all media in collection
     */
    public function getSecureImageUrls(string $collectionName, string $conversionName = ''): array
    {
        $mediaItems = $this->getMedia($collectionName);
        $service = app(SecureImageService::class);
        
        return $mediaItems->map(function (Media $media) use ($service, $conversionName) {
            return $service->getSecureImageUrl($media, $conversionName);
        })->toArray();
    }

    /**
     * Check if media is encrypted
     */
    public function isMediaEncrypted(string $collectionName): bool
    {
        $media = $this->getFirstMedia($collectionName);
        
        if (!$media) {
            return false;
        }

        $service = app(SecureImageService::class);
        
        return $service->isMediaEncrypted($media);
    }

    /**
     * Delete secure image from media collection
     */
    public function deleteSecureImage(string $collectionName): bool
    {
        $media = $this->getFirstMedia($collectionName);
        
        if (!$media) {
            return false;
        }

        $service = app(SecureImageService::class);
        
        return $service->deleteSecureImageFromMedia($media);
    }

    /**
     * Clear all secure images from collection
     */
    public function clearSecureImages(string $collectionName): bool
    {
        $mediaItems = $this->getMedia($collectionName);
        $service = app(SecureImageService::class);
        
        $success = true;
        foreach ($mediaItems as $media) {
            if (!$service->deleteSecureImageFromMedia($media)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Register secure media collections
     */
    public function registerSecureMediaCollections(): void
    {
        $service = app(SecureImageService::class);
        
        // Define secure collections for this model
        $collections = $this->getSecureMediaCollections();
        
        $service->registerSecureMediaCollections($this, $collections);
    }

    /**
     * Get secure media collections configuration
     * Override this method in your model to define collections
     */
    protected function getSecureMediaCollections(): array
    {
        return [
            $service->createSecureMediaCollection('default'),
        ];
    }

    /**
     * Get secure media with fallback to regular media
     */
    public function getSecureImageUrlWithFallback(string $collectionName, string $conversionName = '', ?string $fallbackUrl = null): ?string
    {
        $url = $this->getSecureImageUrl($collectionName, $conversionName);
        
        if ($url) {
            return $url;
        }

        // Try regular media URL
        $media = $this->getFirstMedia($collectionName);
        if ($media) {
            return $conversionName ? $media->getUrl($conversionName) : $media->getUrl();
        }

        return $fallbackUrl;
    }

    /**
     * Get secure image data (for API responses)
     */
    public function getSecureImageData(string $collectionName, string $conversionName = ''): ?array
    {
        $media = $this->getFirstMedia($collectionName);
        
        if (!$media) {
            return null;
        }

        $service = app(SecureImageService::class);
        
        if (!$service->isMediaEncrypted($media)) {
            return [
                'url' => $conversionName ? $media->getUrl($conversionName) : $media->getUrl(),
                'is_encrypted' => false,
                'name' => $media->name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ];
        }

        return [
            'url' => $service->getSecureImageUrl($media, $conversionName),
            'is_encrypted' => true,
            'name' => $media->custom_properties['original_name'] ?? $media->name,
            'size' => $media->size,
            'mime_type' => $media->mime_type,
            'encrypted_at' => $media->custom_properties['encrypted_at'] ?? null,
        ];
    }
}