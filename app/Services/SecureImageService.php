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
}