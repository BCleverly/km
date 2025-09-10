<?php

declare(strict_types=1);

namespace App\Actions\Images;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Lorisleiva\Actions\Concerns\AsAction;

class DecryptImageAction
{
    use AsAction;

    public function __construct(
        private Encrypter $encrypter
    ) {}

    /**
     * Decrypt image content
     */
    public function handle(string $encryptedContent): array
    {
        if (empty($encryptedContent)) {
            throw new \InvalidArgumentException('Encrypted content cannot be empty');
        }

        try {
            // Decrypt the data structure
            $decryptedData = $this->encrypter->decrypt($encryptedContent);

            // Validate the decrypted data structure
            if (!is_array($decryptedData) || !isset($decryptedData['content'])) {
                throw new \RuntimeException('Invalid decrypted data structure');
            }

            // Return the decrypted content with metadata
            return [
                'content' => $decryptedData['content'],
                'mime_type' => $this->detectMimeType($decryptedData['content']),
                'original_name' => $decryptedData['metadata']['original_name'] ?? null,
                'encrypted_at' => $decryptedData['encrypted_at'] ?? null,
                'version' => $decryptedData['version'] ?? '1.0',
                'metadata' => $decryptedData['metadata'] ?? [],
            ];

        } catch (DecryptException $e) {
            throw new \RuntimeException('Failed to decrypt image content: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Decrypt image content and return only the binary content
     */
    public function handleToContent(string $encryptedContent): string
    {
        $decryptedData = $this->handle($encryptedContent);
        return $decryptedData['content'];
    }

    /**
     * Decrypt image content and return with specific metadata
     */
    public function handleWithMetadata(string $encryptedContent, array $requiredMetadata = []): array
    {
        $decryptedData = $this->handle($encryptedContent);

        // Check if required metadata is present
        foreach ($requiredMetadata as $key) {
            if (!isset($decryptedData['metadata'][$key])) {
                throw new \RuntimeException("Required metadata '{$key}' not found in decrypted data");
            }
        }

        return $decryptedData;
    }

    /**
     * Decrypt image content and validate it's a valid image
     */
    public function handleAndValidate(string $encryptedContent): array
    {
        $decryptedData = $this->handle($encryptedContent);
        
        // Validate that the content is actually an image
        $content = $decryptedData['content'];
        
        if (empty($content)) {
            throw new \RuntimeException('Decrypted content is empty');
        }

        // Check if content starts with valid image headers
        $imageHeaders = [
            "\xFF\xD8\xFF", // JPEG
            "\x89PNG\r\n\x1a\n", // PNG
            "GIF87a", // GIF87a
            "GIF89a", // GIF89a
            "RIFF", // WebP (starts with RIFF)
            "BM", // BMP
            "II*\x00", // TIFF (little endian)
            "MM\x00*", // TIFF (big endian)
        ];

        $isValidImage = false;
        foreach ($imageHeaders as $header) {
            if (str_starts_with($content, $header)) {
                $isValidImage = true;
                break;
            }
        }

        // Check for SVG (text-based)
        if (!$isValidImage && str_contains($content, '<svg')) {
            $isValidImage = true;
        }

        if (!$isValidImage) {
            throw new \RuntimeException('Decrypted content does not appear to be a valid image');
        }

        return $decryptedData;
    }

    /**
     * Detect MIME type from image content
     */
    private function detectMimeType(string $content): ?string
    {
        if (empty($content)) {
            return null;
        }

        // Check for common image signatures
        if (str_starts_with($content, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        if (str_starts_with($content, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }

        if (str_starts_with($content, "GIF87a") || str_starts_with($content, "GIF89a")) {
            return 'image/gif';
        }

        if (str_starts_with($content, "RIFF") && str_contains($content, "WEBP")) {
            return 'image/webp';
        }

        if (str_starts_with($content, "BM")) {
            return 'image/bmp';
        }

        if (str_starts_with($content, "II*\x00") || str_starts_with($content, "MM\x00*")) {
            return 'image/tiff';
        }

        if (str_contains($content, '<svg')) {
            return 'image/svg+xml';
        }

        // Try to detect AVIF (more complex signature)
        if (str_starts_with($content, "\x00\x00\x00") && str_contains($content, "ftypavif")) {
            return 'image/avif';
        }

        return 'application/octet-stream'; // Fallback
    }

    /**
     * Get metadata from encrypted content without full decryption
     */
    public function getMetadata(string $encryptedContent): array
    {
        try {
            $decryptedData = $this->encrypter->decrypt($encryptedContent);
            
            return [
                'encrypted_at' => $decryptedData['encrypted_at'] ?? null,
                'version' => $decryptedData['version'] ?? '1.0',
                'metadata' => $decryptedData['metadata'] ?? [],
                'has_content' => isset($decryptedData['content']),
            ];
        } catch (DecryptException $e) {
            throw new \RuntimeException('Failed to decrypt metadata: ' . $e->getMessage(), 0, $e);
        }
    }
}