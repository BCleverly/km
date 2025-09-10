<?php

declare(strict_types=1);

use App\Filesystem\EncryptedDisk;
use App\Services\FileEncryptionService;
use Illuminate\Support\Facades\Storage;

it('can write and read encrypted content', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content that should be encrypted.';
    $testPath = 'test-file.txt';
    
    // Write encrypted content
    $result = $disk->put($testPath, $testContent);
    expect($result)->toBeTrue();
    
    // Read and decrypt content
    $decryptedContent = $disk->get($testPath);
    expect($decryptedContent)->toBe($testContent);
    
    // Clean up
    $disk->delete($testPath);
});

it('can get content with metadata', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content.';
    $testPath = 'test-file-with-metadata.txt';
    
    // Write encrypted content
    $disk->put($testPath, $testContent);
    
    // Get content with metadata
    $result = $disk->getWithMetadata($testPath);
    
    expect($result)->toHaveKeys(['content', 'key_id', 'needs_reencryption']);
    expect($result['content'])->toBe($testContent);
    expect($result['key_id'])->toBeString();
    expect($result['needs_reencryption'])->toBeFalse();
    
    // Clean up
    $disk->delete($testPath);
});

it('can copy encrypted files', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content.';
    $sourcePath = 'source-file.txt';
    $destPath = 'dest-file.txt';
    
    // Write source file
    $disk->put($sourcePath, $testContent);
    
    // Copy file
    $result = $disk->copy($sourcePath, $destPath);
    expect($result)->toBeTrue();
    
    // Verify both files exist and have same content
    expect($disk->exists($sourcePath))->toBeTrue();
    expect($disk->exists($destPath))->toBeTrue();
    expect($disk->get($destPath))->toBe($testContent);
    
    // Clean up
    $disk->delete($sourcePath);
    $disk->delete($destPath);
});

it('can move encrypted files', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content.';
    $sourcePath = 'source-file.txt';
    $destPath = 'dest-file.txt';
    
    // Write source file
    $disk->put($sourcePath, $testContent);
    
    // Move file
    $result = $disk->move($sourcePath, $destPath);
    expect($result)->toBeTrue();
    
    // Verify source is gone and dest exists
    expect($disk->exists($sourcePath))->toBeFalse();
    expect($disk->exists($destPath))->toBeTrue();
    expect($disk->get($destPath))->toBe($testContent);
    
    // Clean up
    $disk->delete($destPath);
});

it('can create read stream for encrypted content', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content.';
    $testPath = 'test-stream.txt';
    
    // Write encrypted content
    $disk->put($testPath, $testContent);
    
    // Create read stream
    $stream = $disk->readStream($testPath);
    expect($stream)->toBeResource();
    
    $streamContent = stream_get_contents($stream);
    expect($streamContent)->toBe($testContent);
    
    fclose($stream);
    
    // Clean up
    $disk->delete($testPath);
});

it('can create write stream for encrypted content', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is a test file content.';
    $testPath = 'test-write-stream.txt';
    
    // Create write stream
    $stream = fopen('php://temp', 'r+');
    fwrite($stream, $testContent);
    rewind($stream);
    
    $result = $disk->writeStream($testPath, $stream);
    expect($result)->toBeTrue();
    
    fclose($stream);
    
    // Verify content
    expect($disk->get($testPath))->toBe($testContent);
    
    // Clean up
    $disk->delete($testPath);
});

it('handles non-encrypted content for backward compatibility', function () {
    $disk = Storage::disk('encrypted');
    $testContent = 'This is plain text content.';
    $testPath = 'plain-text-file.txt';
    
    // Write directly to underlying filesystem (bypassing encryption)
    $underlyingDisk = Storage::disk('local');
    $underlyingDisk->put($testPath, $testContent);
    
    // Move to encrypted disk location
    $encryptedPath = 'encrypted/' . $testPath;
    $underlyingDisk->move($testPath, $encryptedPath);
    
    // Read from encrypted disk (should handle non-encrypted content)
    $result = $disk->get($encryptedPath);
    expect($result)->toBe($testContent);
    
    // Clean up
    $underlyingDisk->delete($encryptedPath);
});