<?php

declare(strict_types=1);

use App\Services\FileEncryptionService;
use Illuminate\Contracts\Encryption\Encrypter;

it('can encrypt and decrypt content', function () {
    $encryptionService = app(FileEncryptionService::class);
    $originalContent = 'This is a test file content that needs to be encrypted.';
    
    $encrypted = $encryptionService->encrypt($originalContent);
    $decrypted = $encryptionService->decrypt($encrypted);
    
    expect($encrypted)->not->toBe($originalContent);
    expect($decrypted)->toBe($originalContent);
});

it('can detect encrypted content', function () {
    $encryptionService = app(FileEncryptionService::class);
    $originalContent = 'This is a test file content.';
    
    $encrypted = $encryptionService->encrypt($originalContent);
    
    expect($encryptionService->isEncrypted($encrypted))->toBeTrue();
    expect($encryptionService->isEncrypted($originalContent))->toBeFalse();
});

it('can get current key id', function () {
    $encryptionService = app(FileEncryptionService::class);
    $keyId = $encryptionService->getCurrentKeyId();
    
    expect($keyId)->toBeString();
    expect(strlen($keyId))->toBe(64); // SHA256 hash length
});

it('can decrypt with fallback to historical keys', function () {
    $encryptionService = app(FileEncryptionService::class);
    $originalContent = 'This is a test file content.';
    
    // Encrypt with current key
    $encrypted = $encryptionService->encrypt($originalContent);
    
    // Decrypt with fallback (should work with current key)
    $result = $encryptionService->decryptWithFallback($encrypted);
    
    expect($result['content'])->toBe($originalContent);
    expect($result['key_id'])->toBe($encryptionService->getCurrentKeyId());
    expect($result['needs_reencryption'])->toBeFalse();
});

it('can re-encrypt content', function () {
    $encryptionService = app(FileEncryptionService::class);
    $originalContent = 'This is a test file content.';
    
    $encrypted = $encryptionService->encrypt($originalContent);
    $reencrypted = $encryptionService->reencrypt($originalContent);
    
    expect($encrypted)->not->toBe($reencrypted);
    
    $decrypted = $encryptionService->decrypt($reencrypted);
    expect($decrypted)->toBe($originalContent);
});

it('throws exception for invalid encrypted content', function () {
    $encryptionService = app(FileEncryptionService::class);
    
    expect(fn() => $encryptionService->decrypt('invalid-content'))
        ->toThrow(RuntimeException::class, 'Invalid encrypted content');
});

it('throws exception for decryption failure', function () {
    $encryptionService = app(FileEncryptionService::class);
    
    // Create invalid encrypted content
    $invalidEncrypted = base64_encode('invalid-encrypted-data');
    
    expect(fn() => $encryptionService->decrypt($invalidEncrypted))
        ->toThrow(RuntimeException::class, 'Failed to decrypt file content');
});