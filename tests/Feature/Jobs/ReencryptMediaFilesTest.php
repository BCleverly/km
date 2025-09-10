<?php

declare(strict_types=1);

use App\Jobs\ReencryptMediaFiles;
use App\Models\Media;
use App\Models\User;
use App\Services\FileEncryptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('encrypted');
    Storage::fake('public');
});

it('can re-encrypt media files', function () {
    Queue::fake();
    
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Create test content and store it encrypted
    $testContent = 'This is test image content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    // Set old encryption key ID
    $media->setCustomProperty('encryption_key_id', 'old-key-id');
    $media->save();
    
    // Dispatch re-encryption job
    $job = new ReencryptMediaFiles($media->id, 'old-key-id');
    $job->handle(app(FileEncryptionService::class));
    
    // Verify media was updated
    $media->refresh();
    expect($media->getEncryptionKeyId())->toBe(app(FileEncryptionService::class)->getCurrentKeyId());
    expect($media->getEncryptedAt())->not->toBeNull();
    
    // Verify file content is still accessible
    $decryptedContent = Storage::disk('encrypted')->get($media->getPath());
    expect($decryptedContent)->toBe($testContent);
});

it('handles missing media file gracefully', function () {
    Queue::fake();
    
    $job = new ReencryptMediaFiles(999, 'old-key-id');
    
    // Should not throw exception
    $job->handle(app(FileEncryptionService::class));
    
    // No assertions needed - just ensuring no exception is thrown
})->expectNotToPerformAssertions();

it('handles missing file on disk gracefully', function () {
    Queue::fake();
    
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'missing-file.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Don't create the actual file on disk
    
    $job = new ReencryptMediaFiles($media->id, 'old-key-id');
    
    // Should not throw exception
    $job->handle(app(FileEncryptionService::class));
    
    // No assertions needed - just ensuring no exception is thrown
})->expectNotToPerformAssertions();

it('skips re-encryption if not needed', function () {
    Queue::fake();
    
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Create test content and store it encrypted
    $testContent = 'This is test image content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    // Set current encryption key ID
    $currentKeyId = app(FileEncryptionService::class)->getCurrentKeyId();
    $media->setCustomProperty('encryption_key_id', $currentKeyId);
    $media->save();
    
    $job = new ReencryptMediaFiles($media->id, $currentKeyId);
    $job->handle(app(FileEncryptionService::class));
    
    // Verify media was not changed
    $media->refresh();
    expect($media->getEncryptionKeyId())->toBe($currentKeyId);
});

it('can be dispatched as a job', function () {
    Queue::fake();
    
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Dispatch the job
    ReencryptMediaFiles::dispatch($media->id, 'old-key-id');
    
    // Verify job was dispatched
    Queue::assertPushed(ReencryptMediaFiles::class, function ($job) use ($media) {
        return $job->mediaId === $media->id;
    });
});