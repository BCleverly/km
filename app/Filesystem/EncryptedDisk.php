<?php

declare(strict_types=1);

namespace App\Filesystem;

use App\Services\FileEncryptionService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class EncryptedDisk extends FilesystemAdapter
{
    public function __construct(
        private FileEncryptionService $encryptionService,
        string $root,
        array $config = []
    ) {
        $adapter = new LocalFilesystemAdapter($root);
        $filesystem = new Flysystem($adapter, $config);
        
        parent::__construct($filesystem, $config);
    }

    /**
     * Write encrypted content to file
     */
    public function put(string $path, $contents, $options = []): bool
    {
        $encryptedContents = $this->encryptionService->encrypt((string) $contents);
        return parent::put($path, $encryptedContents, $options);
    }

    /**
     * Read and decrypt file content
     */
    public function get(string $path): string
    {
        $encryptedContent = parent::get($path);
        
        if ($this->encryptionService->isEncrypted($encryptedContent)) {
            $result = $this->encryptionService->decryptWithFallback($encryptedContent);
            return $result['content'];
        }
        
        // If not encrypted, return as-is (for backward compatibility)
        return $encryptedContent;
    }

    /**
     * Read and decrypt file content with metadata about encryption
     */
    public function getWithMetadata(string $path): array
    {
        $encryptedContent = parent::get($path);
        
        if ($this->encryptionService->isEncrypted($encryptedContent)) {
            return $this->encryptionService->decryptWithFallback($encryptedContent);
        }
        
        return [
            'content' => $encryptedContent,
            'key_id' => null,
            'needs_reencryption' => false
        ];
    }

    /**
     * Copy file with encryption
     */
    public function copy(string $from, string $to): bool
    {
        $content = $this->get($from);
        return $this->put($to, $content);
    }

    /**
     * Move file with encryption
     */
    public function move(string $from, string $to): bool
    {
        if ($this->copy($from, $to)) {
            return $this->delete($from);
        }
        
        return false;
    }

    /**
     * Create a stream for reading encrypted content
     */
    public function readStream(string $path)
    {
        $content = $this->get($path);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        
        return $stream;
    }

    /**
     * Create a stream for writing encrypted content
     */
    public function writeStream(string $path, $resource, array $options = []): bool
    {
        $content = stream_get_contents($resource);
        return $this->put($path, $content, $options);
    }

    /**
     * Get file size (this will be the encrypted size)
     */
    public function size(string $path): int
    {
        return parent::size($path);
    }

    /**
     * Get the last modified time
     */
    public function lastModified(string $path): int
    {
        return parent::lastModified($path);
    }

    /**
     * Check if file exists
     */
    public function exists(string $path): bool
    {
        return parent::exists($path);
    }

    /**
     * Delete file
     */
    public function delete(string $path): bool
    {
        return parent::delete($path);
    }

    /**
     * Get all files in directory
     */
    public function files(string $directory = null): array
    {
        return parent::files($directory);
    }

    /**
     * Get all directories
     */
    public function directories(string $directory = null): array
    {
        return parent::directories($directory);
    }

    /**
     * Make directory
     */
    public function makeDirectory(string $path): bool
    {
        return parent::makeDirectory($path);
    }

    /**
     * Delete directory
     */
    public function deleteDirectory(string $directory): bool
    {
        return parent::deleteDirectory($directory);
    }

    /**
     * Get the underlying filesystem
     */
    public function getDriver(): Flysystem
    {
        return $this->driver;
    }
}