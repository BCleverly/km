<?php

declare(strict_types=1);

namespace App\Actions\Images;

use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidateImageAction
{
    use AsAction;

    /**
     * Supported image MIME types
     */
    private array $supportedMimeTypes = [
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

    /**
     * Supported image extensions
     */
    private array $supportedExtensions = [
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

    /**
     * Maximum file size in bytes (10MB default)
     */
    private int $maxFileSize = 10 * 1024 * 1024;

    /**
     * Validate an uploaded file
     */
    public function handle(UploadedFile $file): array
    {
        // Check if file is valid
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => 'Invalid file upload: ' . $file->getErrorMessage(),
            ];
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'File size exceeds maximum allowed size of ' . $this->formatBytes($this->maxFileSize),
            ];
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $this->supportedMimeTypes, true)) {
            return [
                'valid' => false,
                'message' => "Unsupported file type: {$mimeType}. Supported types: " . implode(', ', $this->supportedMimeTypes),
            ];
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->supportedExtensions, true)) {
            return [
                'valid' => false,
                'message' => "Unsupported file extension: {$extension}. Supported extensions: " . implode(', ', $this->supportedExtensions),
            ];
        }

        // Additional validation for specific image types
        $contentValidation = $this->validateImageContent($file->getPathname());
        if (!$contentValidation['valid']) {
            return $contentValidation;
        }

        return [
            'valid' => true,
            'message' => 'Image validation passed',
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $file->getSize(),
        ];
    }

    /**
     * Validate image content from string/binary data
     */
    public function handleFromContent(string $content, string $mimeType): array
    {
        if (empty($content)) {
            return [
                'valid' => false,
                'message' => 'Image content is empty',
            ];
        }

        // Check content size
        if (strlen($content) > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'Image content size exceeds maximum allowed size of ' . $this->formatBytes($this->maxFileSize),
            ];
        }

        // Check MIME type
        if (!in_array($mimeType, $this->supportedMimeTypes, true)) {
            return [
                'valid' => false,
                'message' => "Unsupported MIME type: {$mimeType}. Supported types: " . implode(', ', $this->supportedMimeTypes),
            ];
        }

        // Validate image content signatures
        $signatureValidation = $this->validateImageSignature($content, $mimeType);
        if (!$signatureValidation['valid']) {
            return $signatureValidation;
        }

        return [
            'valid' => true,
            'message' => 'Image content validation passed',
            'mime_type' => $mimeType,
            'size' => strlen($content),
        ];
    }

    /**
     * Validate image content by checking file signatures
     */
    private function validateImageContent(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'valid' => false,
                'message' => 'File does not exist',
            ];
        }

        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB for signature check
        
        if ($content === false) {
            return [
                'valid' => false,
                'message' => 'Failed to read file content',
            ];
        }

        return $this->validateImageSignature($content);
    }

    /**
     * Validate image signature against known image formats
     */
    private function validateImageSignature(string $content, ?string $expectedMimeType = null): array
    {
        if (empty($content)) {
            return [
                'valid' => false,
                'message' => 'Content is empty',
            ];
        }

        $detectedType = $this->detectImageType($content);
        
        if (!$detectedType) {
            return [
                'valid' => false,
                'message' => 'Content does not appear to be a valid image format',
            ];
        }

        // If expected MIME type is provided, verify it matches
        if ($expectedMimeType && $detectedType !== $expectedMimeType) {
            return [
                'valid' => false,
                'message' => "MIME type mismatch. Expected: {$expectedMimeType}, Detected: {$detectedType}",
            ];
        }

        return [
            'valid' => true,
            'message' => 'Image signature validation passed',
            'detected_type' => $detectedType,
        ];
    }

    /**
     * Detect image type from content signature
     */
    private function detectImageType(string $content): ?string
    {
        // JPEG
        if (str_starts_with($content, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        // PNG
        if (str_starts_with($content, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }

        // GIF
        if (str_starts_with($content, "GIF87a") || str_starts_with($content, "GIF89a")) {
            return 'image/gif';
        }

        // WebP
        if (str_starts_with($content, "RIFF") && str_contains($content, "WEBP")) {
            return 'image/webp';
        }

        // BMP
        if (str_starts_with($content, "BM")) {
            return 'image/bmp';
        }

        // TIFF
        if (str_starts_with($content, "II*\x00") || str_starts_with($content, "MM\x00*")) {
            return 'image/tiff';
        }

        // SVG (text-based)
        if (str_contains($content, '<svg')) {
            return 'image/svg+xml';
        }

        // AVIF
        if (str_starts_with($content, "\x00\x00\x00") && str_contains($content, "ftypavif")) {
            return 'image/avif';
        }

        return null;
    }

    /**
     * Set maximum file size
     */
    public function setMaxFileSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;
        return $this;
    }

    /**
     * Get maximum file size
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * Get supported MIME types
     */
    public function getSupportedMimeTypes(): array
    {
        return $this->supportedMimeTypes;
    }

    /**
     * Get supported extensions
     */
    public function getSupportedExtensions(): array
    {
        return $this->supportedExtensions;
    }

    /**
     * Add supported MIME type
     */
    public function addSupportedMimeType(string $mimeType): self
    {
        if (!in_array($mimeType, $this->supportedMimeTypes, true)) {
            $this->supportedMimeTypes[] = $mimeType;
        }
        return $this;
    }

    /**
     * Add supported extension
     */
    public function addSupportedExtension(string $extension): self
    {
        $extension = strtolower($extension);
        if (!in_array($extension, $this->supportedExtensions, true)) {
            $this->supportedExtensions[] = $extension;
        }
        return $this;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}