<?php

use App\Actions\Images\DecryptImageAction;
use App\Actions\Images\EncryptImageAction;
use Illuminate\Contracts\Encryption\DecryptException;

it('can decrypt image content', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    $originalContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    // Encrypt the content
    $encryptedData = $encryptAction->handleFromContent($originalContent);
    
    // Decrypt the content
    $decryptedData = $decryptAction->handle($encryptedData);
    
    expect($decryptedData['content'])->toBe($originalContent);
    expect($decryptedData['mime_type'])->toBe('image/jpeg');
    expect($decryptedData['encrypted_at'])->not->toBeNull();
    expect($decryptedData['version'])->toBe('1.0');
});

it('can decrypt image content to binary only', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    $originalContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    // Encrypt the content
    $encryptedData = $encryptAction->handleFromContent($originalContent);
    
    // Decrypt to content only
    $decryptedContent = $decryptAction->handleToContent($encryptedData);
    
    expect($decryptedContent)->toBe($originalContent);
});

it('can decrypt image with metadata', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    $originalContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $metadata = [
        'original_name' => 'test.jpg',
        'uploaded_by' => 'user123',
    ];
    
    // Encrypt with metadata
    $encryptedData = $encryptAction->handleWithMetadata($originalContent, $metadata);
    
    // Decrypt with required metadata check
    $decryptedData = $decryptAction->handleWithMetadata($encryptedData, ['original_name']);
    
    expect($decryptedData['content'])->toBe($originalContent);
    expect($decryptedData['metadata']['original_name'])->toBe('test.jpg');
    expect($decryptedData['metadata']['uploaded_by'])->toBe('user123');
});

it('can validate decrypted image content', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    $originalContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    // Encrypt the content
    $encryptedData = $encryptAction->handleFromContent($originalContent);
    
    // Decrypt and validate
    $decryptedData = $decryptAction->handleAndValidate($encryptedData);
    
    expect($decryptedData['content'])->toBe($originalContent);
    expect($decryptedData['mime_type'])->toBe('image/jpeg');
    expect($decryptedData['detected_type'])->toBe('image/jpeg');
});

it('can get metadata without full decryption', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    $originalContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $metadata = [
        'original_name' => 'test.jpg',
        'uploaded_by' => 'user123',
    ];
    
    // Encrypt with metadata
    $encryptedData = $encryptAction->handleWithMetadata($originalContent, $metadata);
    
    // Get metadata only
    $metadataOnly = $decryptAction->getMetadata($encryptedData);
    
    expect($metadataOnly['encrypted_at'])->not->toBeNull();
    expect($metadataOnly['version'])->toBe('1.0');
    expect($metadataOnly['metadata']['original_name'])->toBe('test.jpg');
    expect($metadataOnly['metadata']['uploaded_by'])->toBe('user123');
    expect($metadataOnly['has_content'])->toBeTrue();
});

it('throws exception for invalid encrypted content', function () {
    $decryptAction = app(DecryptImageAction::class);
    
    expect(fn() => $decryptAction->handle('invalid-encrypted-data'))
        ->toThrow(\RuntimeException::class, 'Failed to decrypt image content');
});

it('throws exception for empty encrypted content', function () {
    $decryptAction = app(DecryptImageAction::class);
    
    expect(fn() => $decryptAction->handle(''))
        ->toThrow(\InvalidArgumentException::class, 'Encrypted content cannot be empty');
});

it('throws exception for invalid image content', function () {
    $encryptAction = app(EncryptImageAction::class);
    $decryptAction = app(DecryptImageAction::class);
    
    // Encrypt non-image content
    $encryptedData = $encryptAction->handleFromContent('not an image');
    
    expect(fn() => $decryptAction->handleAndValidate($encryptedData))
        ->toThrow(\RuntimeException::class, 'Decrypted content does not appear to be a valid image format');
});