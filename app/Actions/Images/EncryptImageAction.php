<?php

declare(strict_types=1);

namespace App\Actions\Images;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\Concerns\AsAction;

class EncryptImageAction
{
    use AsAction;

    public function __construct(
        private Encrypter $encrypter
    ) {}

    /**
     * Encrypt image content from file path
     */
    public function handle(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$filePath}");
        }

        return $this->handleFromContent($content);
    }

    /**
     * Encrypt image content from string/binary data
     */
    public function handleFromContent(string $content): string
    {
        if (empty($content)) {
            throw new \InvalidArgumentException('Image content cannot be empty');
        }

        // Create a data structure that includes the content and metadata
        $imageData = [
            'content' => $content,
            'encrypted_at' => now()->toISOString(),
            'version' => '1.0', // For future compatibility
        ];

        // Encrypt the entire data structure
        $encryptedData = $this->encrypter->encrypt($imageData);

        return $encryptedData;
    }

    /**
     * Encrypt image with additional metadata
     */
    public function handleWithMetadata(string $content, array $metadata = []): string
    {
        if (empty($content)) {
            throw new \InvalidArgumentException('Image content cannot be empty');
        }

        // Create a data structure that includes the content and metadata
        $imageData = [
            'content' => $content,
            'metadata' => $metadata,
            'encrypted_at' => now()->toISOString(),
            'version' => '1.0', // For future compatibility
        ];

        // Encrypt the entire data structure
        $encryptedData = $this->encrypter->encrypt($imageData);

        return $encryptedData;
    }

    /**
     * Encrypt image from file path with additional metadata
     */
    public function handleFromFileWithMetadata(string $filePath, array $metadata = []): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$filePath}");
        }

        // Add file-specific metadata
        $fileMetadata = array_merge($metadata, [
            'file_size' => filesize($filePath),
            'file_modified' => filemtime($filePath),
        ]);

        return $this->handleWithMetadata($content, $fileMetadata);
    }
}