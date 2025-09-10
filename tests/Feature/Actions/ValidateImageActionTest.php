<?php

use App\Actions\Images\ValidateImageAction;
use Illuminate\Http\UploadedFile;

it('can validate uploaded image file', function () {
    $action = app(ValidateImageAction::class);
    
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    
    $result = $action->handle($file);
    
    expect($result['valid'])->toBeTrue();
    expect($result['message'])->toBe('Image validation passed');
    expect($result['mime_type'])->toBe('image/jpeg');
    expect($result['extension'])->toBe('jpg');
    expect($result['size'])->toBeGreaterThan(0);
});

it('can validate image content', function () {
    $action = app(ValidateImageAction::class);
    
    // Create fake JPEG content
    $content = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $result = $action->handleFromContent($content, 'image/jpeg');
    
    expect($result['valid'])->toBeTrue();
    expect($result['message'])->toBe('Image content validation passed');
    expect($result['mime_type'])->toBe('image/jpeg');
    expect($result['size'])->toBe(strlen($content));
});

it('rejects invalid file types', function () {
    $action = app(ValidateImageAction::class);
    
    $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
    
    $result = $action->handle($file);
    
    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Unsupported file type');
});

it('rejects invalid MIME types', function () {
    $action = app(ValidateImageAction::class);
    
    $result = $action->handleFromContent('some content', 'text/plain');
    
    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Unsupported MIME type');
});

it('rejects files that exceed size limit', function () {
    $action = app(ValidateImageAction::class);
    
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $file->size = 15 * 1024 * 1024; // 15MB (exceeds 10MB limit)
    
    $result = $action->handle($file);
    
    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('File size exceeds maximum');
});

it('rejects content that exceeds size limit', function () {
    $action = app(ValidateImageAction::class);
    
    // Create content larger than 10MB
    $content = str_repeat('A', 11 * 1024 * 1024);
    
    $result = $action->handleFromContent($content, 'image/jpeg');
    
    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Image content size exceeds maximum');
});

it('rejects empty content', function () {
    $action = app(ValidateImageAction::class);
    
    $result = $action->handleFromContent('', 'image/jpeg');
    
    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toBe('Image content is empty');
});

it('can detect different image types', function () {
    $action = app(ValidateImageAction::class);
    
    // Test JPEG
    $jpegContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $result = $action->handleFromContent($jpegContent, 'image/jpeg');
    expect($result['valid'])->toBeTrue();
    
    // Test PNG
    $pngContent = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x02\x00\x00\x00\x90wS\xde\x00\x00\x00\tpHYs\x00\x00\x0b\x13\x00\x00\x0b\x13\x01\x00\x9a\x9c\x18\x00\x00\x00\nIDATx\x9cc```\x00\x00\x00\x04\x00\x01\xdd\x8d\xb4\x1c\x00\x00\x00\x00IEND\xaeB`\x82";
    
    $result = $action->handleFromContent($pngContent, 'image/png');
    expect($result['valid'])->toBeTrue();
    
    // Test GIF
    $gifContent = "GIF87a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;";
    
    $result = $action->handleFromContent($gifContent, 'image/gif');
    expect($result['valid'])->toBeTrue();
});

it('can set and get maximum file size', function () {
    $action = app(ValidateImageAction::class);
    
    $originalSize = $action->getMaxFileSize();
    expect($originalSize)->toBe(10 * 1024 * 1024); // 10MB
    
    $action->setMaxFileSize(5 * 1024 * 1024); // 5MB
    expect($action->getMaxFileSize())->toBe(5 * 1024 * 1024);
    
    // Reset to original
    $action->setMaxFileSize($originalSize);
});

it('can get supported MIME types and extensions', function () {
    $action = app(ValidateImageAction::class);
    
    $mimeTypes = $action->getSupportedMimeTypes();
    $extensions = $action->getSupportedExtensions();
    
    expect($mimeTypes)->toContain('image/jpeg');
    expect($mimeTypes)->toContain('image/png');
    expect($mimeTypes)->toContain('image/gif');
    expect($mimeTypes)->toContain('image/webp');
    
    expect($extensions)->toContain('jpg');
    expect($extensions)->toContain('png');
    expect($extensions)->toContain('gif');
    expect($extensions)->toContain('webp');
});

it('can add supported MIME types and extensions', function () {
    $action = app(ValidateImageAction::class);
    
    $action->addSupportedMimeType('image/tiff');
    $action->addSupportedExtension('tiff');
    
    expect($action->getSupportedMimeTypes())->toContain('image/tiff');
    expect($action->getSupportedExtensions())->toContain('tiff');
});