<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SecureImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureMediaController extends Controller
{
    public function __construct(
        private SecureImageService $secureImageService
    ) {}

    /**
     * Display secure media with optional conversion
     */
    public function show(Request $request, Media $media, ?string $conversion = null): Response|StreamedResponse
    {
        // Check if media is encrypted
        if (!$this->secureImageService->isMediaEncrypted($media)) {
            abort(404, 'Media not found or not encrypted');
        }

        try {
            // Get decrypted image data
            $imageData = $this->secureImageService->getSecureImageFromMedia($media);
            
            if (!$imageData['success']) {
                abort(404, 'Failed to decrypt media');
            }

            // Handle conversions if requested
            if ($conversion) {
                $imageData = $this->handleConversion($imageData, $conversion, $media);
            }

            // Set appropriate headers
            $headers = [
                'Content-Type' => $imageData['mime_type'],
                'Content-Length' => strlen($imageData['content']),
                'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
                'Last-Modified' => $media->updated_at->format('D, d M Y H:i:s \G\M\T'),
                'ETag' => '"' . md5($imageData['content']) . '"',
            ];

            // Add original filename if available
            if (isset($imageData['original_name'])) {
                $headers['Content-Disposition'] = 'inline; filename="' . $imageData['original_name'] . '"';
            }

            return response($imageData['content'], 200, $headers);

        } catch (\Exception $e) {
            abort(404, 'Failed to retrieve media: ' . $e->getMessage());
        }
    }

    /**
     * Handle image conversions for secure media
     */
    private function handleConversion(array $imageData, string $conversionName, Media $media): array
    {
        // Check if conversion exists for this media
        $conversions = $media->getMediaConversions();
        
        if (!isset($conversions[$conversionName])) {
            // Return original if conversion doesn't exist
            return $imageData;
        }

        try {
            // Create temporary file for conversion
            $tempPath = 'temp/' . uniqid() . '.' . $this->getExtensionFromMimeType($imageData['mime_type']);
            \Storage::disk('local')->put($tempPath, $imageData['content']);
            $tempFullPath = \Storage::disk('local')->path($tempPath);

            // Apply conversion using Spatie Image
            $image = \Spatie\Image\Image::load($tempFullPath);
            $conversion = $conversions[$conversionName];
            
            // Apply conversion manipulations
            foreach ($conversion->getManipulations() as $manipulation) {
                $image = $this->applyManipulation($image, $manipulation);
            }

            // Get converted content
            $convertedContent = $image->encode()->getEncoded();
            $convertedMimeType = $this->getMimeTypeFromExtension($image->getDriver()->getImageMimeType());

            // Clean up temp file
            \Storage::disk('local')->delete($tempPath);

            return [
                'success' => true,
                'content' => $convertedContent,
                'mime_type' => $convertedMimeType,
                'original_name' => $imageData['original_name'] ?? null,
            ];

        } catch (\Exception $e) {
            // Clean up temp file on error
            \Storage::disk('local')->delete($tempPath ?? '');
            
            // Return original if conversion fails
            return $imageData;
        }
    }

    /**
     * Apply manipulation to image
     */
    private function applyManipulation($image, $manipulation)
    {
        $method = $manipulation->getManipulationMethod();
        $arguments = $manipulation->getManipulationArguments();

        switch ($method) {
            case 'width':
                $image->width($arguments[0]);
                break;
            case 'height':
                $image->height($arguments[0]);
                break;
            case 'fit':
                $image->fit($arguments[0], $arguments[1] ?? null, $arguments[2] ?? null);
                break;
            case 'crop':
                $image->crop($arguments[0], $arguments[1], $arguments[2] ?? null, $arguments[3] ?? null);
                break;
            case 'sharpen':
                $image->sharpen($arguments[0]);
                break;
            case 'blur':
                $image->blur($arguments[0]);
                break;
            case 'brightness':
                $image->brightness($arguments[0]);
                break;
            case 'contrast':
                $image->contrast($arguments[0]);
                break;
            case 'gamma':
                $image->gamma($arguments[0]);
                break;
            case 'greyscale':
                $image->greyscale();
                break;
            case 'sepia':
                $image->sepia();
                break;
            case 'quality':
                $image->quality($arguments[0]);
                break;
            case 'format':
                $image->format($arguments[0]);
                break;
        }

        return $image;
    }

    /**
     * Get file extension from MIME type
     */
    private function getExtensionFromMimeType(string $mimeType): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tiff',
            'image/avif' => 'avif',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    /**
     * Get MIME type from extension
     */
    private function getMimeTypeFromExtension(string $extension): string
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            'avif' => 'image/avif',
        ];

        return $mimeTypes[$extension] ?? 'image/jpeg';
    }
}