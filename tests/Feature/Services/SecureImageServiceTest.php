<?php

use App\Actions\Images\DecryptImageAction;
use App\Actions\Images\EncryptImageAction;
use App\Actions\Images\ValidateImageAction;
use App\Models\Profile;
use App\Models\User;
use App\Services\SecureImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

it('can store secure image from uploaded file', function () {
    $service = app(SecureImageService::class);
    
    // Create a fake image file
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    
    $result = $service->storeSecureImage($file, 'public');
    
    expect($result['success'])->toBeTrue();
    expect($result['path'])->not->toBeNull();
    expect($result['encrypted_path'])->not->toBeNull();
    expect($result['original_name'])->toBe('test.jpg');
    expect($result['mime_type'])->toBe('image/jpeg');
    
    // Verify encrypted file exists
    expect(Storage::disk('public')->exists($result['encrypted_path']))->toBeTrue();
});

it('can store secure image from content', function () {
    $service = app(SecureImageService::class);
    
    // Create fake image content (JPEG header + minimal data)
    $content = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $result = $service->storeSecureImageFromContent($content, 'test.jpg', 'image/jpeg', 'public');
    
    expect($result['success'])->toBeTrue();
    expect($result['path'])->not->toBeNull();
    expect($result['encrypted_path'])->not->toBeNull();
    expect($result['original_name'])->toBe('test.jpg');
    expect($result['mime_type'])->toBe('image/jpeg');
    
    // Verify encrypted file exists
    expect(Storage::disk('public')->exists($result['encrypted_path']))->toBeTrue();
});

it('can retrieve and decrypt secure image', function () {
    $service = app(SecureImageService::class);
    
    // Store an image first
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $storeResult = $service->storeSecureImage($file, 'public');
    
    expect($storeResult['success'])->toBeTrue();
    
    // Retrieve and decrypt
    $result = $service->getSecureImage($storeResult['encrypted_path'], 'public');
    
    expect($result['success'])->toBeTrue();
    expect($result['content'])->not->toBeNull();
    expect($result['mime_type'])->toBe('image/jpeg');
});

it('can delete secure image', function () {
    $service = app(SecureImageService::class);
    
    // Store an image first
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $storeResult = $service->storeSecureImage($file, 'public');
    
    expect($storeResult['success'])->toBeTrue();
    expect(Storage::disk('public')->exists($storeResult['encrypted_path']))->toBeTrue();
    
    // Delete the image
    $result = $service->deleteSecureImage($storeResult['encrypted_path'], 'public');
    
    expect($result['success'])->toBeTrue();
    expect(Storage::disk('public')->exists($storeResult['encrypted_path']))->toBeFalse();
});

it('validates image types correctly', function () {
    $service = app(SecureImageService::class);
    
    // Test valid image
    $validFile = UploadedFile::fake()->image('test.jpg', 100, 100);
    $result = $service->storeSecureImage($validFile, 'public');
    expect($result['success'])->toBeTrue();
    
    // Test invalid file type
    $invalidFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
    $result = $service->storeSecureImage($invalidFile, 'public');
    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Unsupported file type');
});

it('handles file size limits', function () {
    $service = app(SecureImageService::class);
    
    // Create a large file (simulate by setting size)
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $file->size = 15 * 1024 * 1024; // 15MB (exceeds 10MB limit)
    
    $result = $service->storeSecureImage($file, 'public');
    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('File size exceeds maximum');
});

it('returns supported mime types and extensions', function () {
    $service = app(SecureImageService::class);
    
    $mimeTypes = $service->getSupportedMimeTypes();
    $extensions = $service->getSupportedExtensions();
    
    expect($mimeTypes)->toContain('image/jpeg');
    expect($mimeTypes)->toContain('image/png');
    expect($mimeTypes)->toContain('image/gif');
    expect($mimeTypes)->toContain('image/webp');
    
    expect($extensions)->toContain('jpg');
    expect($extensions)->toContain('png');
    expect($extensions)->toContain('gif');
    expect($extensions)->toContain('webp');
});