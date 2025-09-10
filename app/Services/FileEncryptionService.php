<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class FileEncryptionService
{
    public function __construct(
        private Encrypter $encrypter
    ) {}

    /**
     * Encrypt file content using the current app key
     */
    public function encrypt(string $content): string
    {
        $key = $this->getCurrentAppKey();
        $iv = random_bytes(16);
        
        $encrypted = openssl_encrypt(
            $content,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt file content');
        }

        // Prepend IV to encrypted data
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt file content using the current app key
     */
    public function decrypt(string $encryptedContent): string
    {
        $key = $this->getCurrentAppKey();
        $data = base64_decode($encryptedContent);
        
        if ($data === false) {
            throw new \RuntimeException('Invalid encrypted content');
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Failed to decrypt file content');
        }

        return $decrypted;
    }

    /**
     * Try to decrypt with current key, fallback to historical keys if needed
     */
    public function decryptWithFallback(string $encryptedContent, ?string $encryptionKeyId = null): array
    {
        $currentKey = $this->getCurrentAppKey();
        
        // Try current key first
        try {
            $decrypted = $this->decryptWithKey($encryptedContent, $currentKey);
            return [
                'content' => $decrypted,
                'key_id' => $this->getCurrentKeyId(),
                'needs_reencryption' => $encryptionKeyId !== $this->getCurrentKeyId()
            ];
        } catch (\RuntimeException $e) {
            // Current key failed, try historical keys
            $historicalKeys = $this->getHistoricalKeys();
            
            foreach ($historicalKeys as $keyId => $key) {
                try {
                    $decrypted = $this->decryptWithKey($encryptedContent, $key);
                    return [
                        'content' => $decrypted,
                        'key_id' => $keyId,
                        'needs_reencryption' => true
                    ];
                } catch (\RuntimeException $e) {
                    continue;
                }
            }
            
            throw new \RuntimeException('Failed to decrypt with any available key');
        }
    }

    /**
     * Decrypt content with a specific key
     */
    public function decryptWithKey(string $encryptedContent, string $key): string
    {
        $data = base64_decode($encryptedContent);
        
        if ($data === false) {
            throw new \RuntimeException('Invalid encrypted content');
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Failed to decrypt file content with provided key');
        }

        return $decrypted;
    }

    /**
     * Re-encrypt content with the current app key
     */
    public function reencrypt(string $content): string
    {
        return $this->encrypt($content);
    }

    /**
     * Get the current app key
     */
    private function getCurrentAppKey(): string
    {
        $key = config('app.key');
        
        if (empty($key)) {
            throw new \RuntimeException('App key not configured');
        }

        // Remove 'base64:' prefix if present
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return $key;
    }

    /**
     * Get current key ID (hash of the key for identification)
     */
    public function getCurrentKeyId(): string
    {
        return hash('sha256', $this->getCurrentAppKey());
    }

    /**
     * Get historical app keys from configuration
     * These should be stored securely and rotated when app key changes
     */
    private function getHistoricalKeys(): array
    {
        $previousKeys = config('app.previous_keys', []);
        $keys = [];

        foreach ($previousKeys as $key) {
            // Remove 'base64:' prefix if present
            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }
            $keyId = hash('sha256', $key);
            $keys[$keyId] = $key;
        }

        return $keys;
    }

    /**
     * Check if content is encrypted (has proper format)
     */
    public function isEncrypted(string $content): bool
    {
        try {
            $data = base64_decode($content);
            return $data !== false && strlen($data) >= 16;
        } catch (\Exception $e) {
            return false;
        }
    }
}