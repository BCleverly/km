<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Images\DecryptImageAction;
use App\Actions\Images\EncryptImageAction;
use App\Actions\Images\ValidateImageAction;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class SecureImageService
{
    public function __construct(
        private EncryptImageAction $encryptImageAction,
        private DecryptImageAction $decryptImageAction,
        private ValidateImageAction $validateImageAction
    ) {}

    /**
     * Store an uploaded image securely with encryption
     */
    public function storeSecureImage(UploadedFile $file, string $disk = 'public', ?string $path = null): array
    {
        // Validate the image first
        $validation = $this->validateImageAction->handle($file);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'path' => null,
                'encrypted_path' => null,
            ];
        }

        // Generate secure path if not provided
        $securePath = $path ?? $this->generateSecurePath($file->getClientOriginalName());
        
        // Store the file temporarily
        $tempPath = $file->store('temp', 'local');
        $tempFullPath = Storage::disk('local')->path($tempPath);
        
        try {
            // Encrypt the image content
            $encryptedContent = $this->encryptImageAction->handle($tempFullPath);
            
            // Store encrypted content
            $encryptedPath = $this->getEncryptedPath($securePath);
            Storage::disk($disk)->put($encryptedPath, $encryptedContent);
            
            // Clean up temp file
            Storage::disk('local')->delete($tempPath);
            
            return [
                'success' => true,
                'message' => 'Image stored securely',
                'path' => $securePath,
                'encrypted_path' => $encryptedPath,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
            
        } catch (\Exception $e) {
            // Clean up temp file on error
            Storage::disk('local')->delete($tempPath);
            
            return [
                'success' => false,
                'message' => 'Failed to encrypt and store image: ' . $e->getMessage(),
                'path' => null,
                'encrypted_path' => null,
            ];
        }
    }

    /**
     * Store image content from string/binary data securely
     */
    public function storeSecureImageFromContent(string $content, string $originalName, string $mimeType, string $disk = 'public', ?string $path = null): array
    {
        // Validate the image content
        $validation = $this->validateImageAction->handleFromContent($content, $mimeType);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'path' => null,
                'encrypted_path' => null,
            ];
        }

        // Generate secure path if not provided
        $securePath = $path ?? $this->generateSecurePath($originalName);
        
        try {
            // Encrypt the image content
            $encryptedContent = $this->encryptImageAction->handleFromContent($content);
            
            // Store encrypted content
            $encryptedPath = $this->getEncryptedPath($securePath);
            Storage::disk($disk)->put($encryptedPath, $encryptedContent);
            
            return [
                'success' => true,
                'message' => 'Image stored securely',
                'path' => $securePath,
                'encrypted_path' => $encryptedPath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => strlen($content),
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to encrypt and store image: ' . $e->getMessage(),
                'path' => null,
                'encrypted_path' => null,
            ];
        }
    }

    /**
     * Retrieve and decrypt an image
     */
    public function getSecureImage(string $encryptedPath, string $disk = 'public'): array
    {
        try {
            if (!Storage::disk($disk)->exists($encryptedPath)) {
                return [
                    'success' => false,
                    'message' => 'Encrypted image not found',
                    'content' => null,
                    'mime_type' => null,
                ];
            }

            $encryptedContent = Storage::disk($disk)->get($encryptedPath);
            
            // Decrypt the content
            $decryptedData = $this->decryptImageAction->handle($encryptedContent);
            
            return [
                'success' => true,
                'message' => 'Image decrypted successfully',
                'content' => $decryptedData['content'],
                'mime_type' => $decryptedData['mime_type'],
                'original_name' => $decryptedData['original_name'] ?? null,
            ];
            
        } catch (DecryptException $e) {
            return [
                'success' => false,
                'message' => 'Failed to decrypt image - invalid or corrupted encryption',
                'content' => null,
                'mime_type' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve image: ' . $e->getMessage(),
                'content' => null,
                'mime_type' => null,
            ];
        }
    }

    /**
     * Delete a secure image
     */
    public function deleteSecureImage(string $encryptedPath, string $disk = 'public'): array
    {
        try {
            if (Storage::disk($disk)->exists($encryptedPath)) {
                Storage::disk($disk)->delete($encryptedPath);
                
                return [
                    'success' => true,
                    'message' => 'Secure image deleted successfully',
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Encrypted image not found',
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if an encrypted image exists
     */
    public function secureImageExists(string $encryptedPath, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($encryptedPath);
    }

    /**
     * Get the size of an encrypted image
     */
    public function getSecureImageSize(string $encryptedPath, string $disk = 'public'): ?int
    {
        try {
            return Storage::disk($disk)->size($encryptedPath);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate a secure path for the image
     */
    private function generateSecurePath(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = Str::uuid() . '.' . $extension;
        
        return 'secure-images/' . now()->format('Y/m/d') . '/' . $filename;
    }

    /**
     * Get the encrypted path (adds .enc extension)
     */
    private function getEncryptedPath(string $path): string
    {
        return $path . '.enc';
    }

    /**
     * Get supported image MIME types
     */
    public function getSupportedMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/bmp',
            'image/tiff',
            'image/avif',
        ];
    }

    /**
     * Get supported image extensions
     */
    public function getSupportedExtensions(): array
    {
        return [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
            'bmp',
            'tiff',
            'tif',
            'avif',
        ];
    }

    /**
     * Add secure image to Media Library model
     */
    public function addSecureImageToMedia(HasMedia $model, UploadedFile $file, string $collectionName = 'default', array $customProperties = []): ?Media
    {
        // Validate the image first
        $validation = $this->validateImageAction->handle($file);
        
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['message']);
        }

        // Store the file temporarily for Media Library processing
        $tempPath = $file->store('temp', 'local');
        $tempFullPath = Storage::disk('local')->path($tempPath);
        
        try {
            // Add to Media Library first (this handles conversions, etc.)
            $media = $model->addMedia($tempFullPath)
                ->usingName($file->getClientOriginalName())
                ->usingFileName($this->generateSecureFileName($file->getClientOriginalName()))
                ->withCustomProperties(array_merge($customProperties, [
                    'is_encrypted' => true,
                    'original_name' => $file->getClientOriginalName(),
                    'encrypted_at' => now()->toISOString(),
                ]))
                ->toMediaCollection($collectionName);

            // Now encrypt the original file and store it
            $encryptedContent = $this->encryptImageAction->handle($tempFullPath);
            $encryptedPath = $this->getEncryptedMediaPath($media);
            
            // Store encrypted content
            Storage::disk($media->disk)->put($encryptedPath, $encryptedContent);
            
            // Update media record with encrypted path
            $media->update([
                'file_name' => $media->file_name . '.enc',
                'custom_properties' => array_merge($media->custom_properties, [
                    'encrypted_path' => $encryptedPath,
                    'is_encrypted' => true,
                ]),
            ]);

            // Clean up temp file
            Storage::disk('local')->delete($tempPath);
            
            return $media;
            
        } catch (\Exception $e) {
            // Clean up temp file on error
            Storage::disk('local')->delete($tempPath);
            throw $e;
        }
    }

    /**
     * Add secure image from content to Media Library model
     */
    public function addSecureImageFromContentToMedia(HasMedia $model, string $content, string $originalName, string $mimeType, string $collectionName = 'default', array $customProperties = []): ?Media
    {
        // Validate the image content
        $validation = $this->validateImageAction->handleFromContent($content, $mimeType);
        
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['message']);
        }

        // Create temporary file for Media Library processing
        $tempPath = 'temp/' . Str::uuid() . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
        Storage::disk('local')->put($tempPath, $content);
        $tempFullPath = Storage::disk('local')->path($tempPath);
        
        try {
            // Add to Media Library first
            $media = $model->addMedia($tempFullPath)
                ->usingName($originalName)
                ->usingFileName($this->generateSecureFileName($originalName))
                ->withCustomProperties(array_merge($customProperties, [
                    'is_encrypted' => true,
                    'original_name' => $originalName,
                    'encrypted_at' => now()->toISOString(),
                ]))
                ->toMediaCollection($collectionName);

            // Encrypt and store the content
            $encryptedContent = $this->encryptImageAction->handleFromContent($content);
            $encryptedPath = $this->getEncryptedMediaPath($media);
            
            Storage::disk($media->disk)->put($encryptedPath, $encryptedContent);
            
            // Update media record
            $media->update([
                'file_name' => $media->file_name . '.enc',
                'custom_properties' => array_merge($media->custom_properties, [
                    'encrypted_path' => $encryptedPath,
                    'is_encrypted' => true,
                ]),
            ]);

            // Clean up temp file
            Storage::disk('local')->delete($tempPath);
            
            return $media;
            
        } catch (\Exception $e) {
            // Clean up temp file on error
            Storage::disk('local')->delete($tempPath);
            throw $e;
        }
    }

    /**
     * Get decrypted image content from Media Library media
     */
    public function getSecureImageFromMedia(Media $media): array
    {
        if (!$this->isMediaEncrypted($media)) {
            throw new \InvalidArgumentException('Media is not encrypted');
        }

        $encryptedPath = $media->custom_properties['encrypted_path'] ?? $media->getPath();
        
        return $this->getSecureImage($encryptedPath, $media->disk);
    }

    /**
     * Check if media is encrypted
     */
    public function isMediaEncrypted(Media $media): bool
    {
        return $media->custom_properties['is_encrypted'] ?? false;
    }

    /**
     * Get secure image URL for Media Library media
     */
    public function getSecureImageUrl(Media $media, string $conversionName = ''): string
    {
        if (!$this->isMediaEncrypted($media)) {
            // Return normal URL if not encrypted
            return $conversionName ? $media->getUrl($conversionName) : $media->getUrl();
        }

        // For encrypted media, we need to create a route that decrypts on-the-fly
        $routeParams = [
            'media' => $media->id,
        ];

        if ($conversionName) {
            $routeParams['conversion'] = $conversionName;
        }

        return route('secure-media.show', $routeParams);
    }

    /**
     * Delete secure image from Media Library
     */
    public function deleteSecureImageFromMedia(Media $media): bool
    {
        if ($this->isMediaEncrypted($media)) {
            $encryptedPath = $media->custom_properties['encrypted_path'] ?? null;
            if ($encryptedPath && Storage::disk($media->disk)->exists($encryptedPath)) {
                Storage::disk($media->disk)->delete($encryptedPath);
            }
        }

        return $media->delete();
    }

    /**
     * Generate secure filename for Media Library
     */
    private function generateSecureFileName(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Get encrypted path for Media Library media
     */
    private function getEncryptedMediaPath(Media $media): string
    {
        $pathGenerator = app(PathGenerator::class);
        $path = $pathGenerator->getPath($media);
        
        return $path . $media->file_name . '.enc';
    }

    /**
     * Create secure media collection configuration
     */
    public function createSecureMediaCollection(string $name, array $mimeTypes = [], bool $singleFile = false): array
    {
        $defaultMimeTypes = $this->getSupportedMimeTypes();
        $allowedMimeTypes = empty($mimeTypes) ? $defaultMimeTypes : array_intersect($mimeTypes, $defaultMimeTypes);

        return [
            'name' => $name,
            'mime_types' => $allowedMimeTypes,
            'single_file' => $singleFile,
            'is_secure' => true,
        ];
    }

    /**
     * Register secure media collections for a model
     */
    public function registerSecureMediaCollections(HasMedia $model, array $collections): void
    {
        foreach ($collections as $collection) {
            $collectionName = $collection['name'];
            $mimeTypes = $collection['mime_types'] ?? $this->getSupportedMimeTypes();
            $singleFile = $collection['single_file'] ?? false;

            $model->addMediaCollection($collectionName)
                ->acceptsMimeTypes($mimeTypes)
                ->when($singleFile, fn($collection) => $collection->singleFile());
        }
    }
}