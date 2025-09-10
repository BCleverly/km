<?php

declare(strict_types=1);

use App\Models\Media;
use App\Models\User;
use App\Services\FileEncryptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('encrypted');
    Storage::fake('public');
});

it('can get and set encryption key id', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
    ]);
    
    $keyId = 'test-key-id-123';
    $media->setEncryptionKeyId($keyId);
    
    expect($media->getEncryptionKeyId())->toBe($keyId);
});

it('can get and set encrypted at timestamp', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
    ]);
    
    $date = now();
    $media->setEncryptedAt($date);
    
    expect($media->getEncryptedAt())->toEqual($date);
});

it('can check if file needs re-encryption', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
    ]);
    
    // With no key ID set, should need re-encryption
    expect($media->needsReencryption())->toBeTrue();
    
    // With current key ID, should not need re-encryption
    $currentKeyId = app(FileEncryptionService::class)->getCurrentKeyId();
    $media->setEncryptionKeyId($currentKeyId);
    expect($media->needsReencryption())->toBeFalse();
    
    // With old key ID, should need re-encryption
    $media->setEncryptionKeyId('old-key-id');
    expect($media->needsReencryption())->toBeTrue();
});

it('can store content to encrypted disk', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'public',
    ]);
    
    $testContent = 'This is test content for encryption.';
    $media->storeToEncryptedDisk($testContent);
    
    // Verify disk was changed
    expect($media->disk)->toBe('encrypted');
    
    // Verify encryption metadata was set
    expect($media->getEncryptionKeyId())->toBe(app(FileEncryptionService::class)->getCurrentKeyId());
    expect($media->getEncryptedAt())->not->toBeNull();
    
    // Verify content is stored and can be retrieved
    expect(Storage::disk('encrypted')->get($media->getPath()))->toBe($testContent);
});

it('can move from current disk to encrypted disk', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'public',
    ]);
    
    $testContent = 'This is test content for encryption.';
    
    // Store content on public disk first
    Storage::disk('public')->put($media->getPath(), $testContent);
    
    // Move to encrypted disk
    $media->moveToEncryptedDisk();
    
    // Verify disk was changed
    expect($media->disk)->toBe('encrypted');
    
    // Verify encryption metadata was set
    expect($media->getEncryptionKeyId())->toBe(app(FileEncryptionService::class)->getCurrentKeyId());
    expect($media->getEncryptedAt())->not->toBeNull();
    
    // Verify content is stored on encrypted disk
    expect(Storage::disk('encrypted')->get($media->getPath()))->toBe($testContent);
    
    // Verify content was removed from public disk
    expect(Storage::disk('public')->exists($media->getPath()))->toBeFalse();
});

it('can check if file is encrypted', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'public',
    ]);
    
    // Not encrypted initially
    expect($media->isEncrypted())->toBeFalse();
    
    // Set encryption metadata but wrong disk
    $media->setEncryptionKeyId('test-key-id');
    expect($media->isEncrypted())->toBeFalse();
    
    // Set correct disk and key ID
    $media->disk = 'encrypted';
    expect($media->isEncrypted())->toBeTrue();
});

it('can get encrypted content', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
    ]);
    
    $testContent = 'This is test content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    expect($media->getEncryptedContent())->toBe($testContent);
});

it('throws exception when getting encrypted content from non-encrypted disk', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'public',
    ]);
    
    expect(fn() => $media->getEncryptedContent())
        ->toThrow(RuntimeException::class, 'Media file is not stored on encrypted disk');
});

it('can get encrypted content with metadata', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
    ]);
    
    $testContent = 'This is test content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $result = $media->getEncryptedContentWithMetadata();
    
    expect($result)->toHaveKeys(['content', 'key_id', 'needs_reencryption']);
    expect($result['content'])->toBe($testContent);
    expect($result['key_id'])->toBeString();
    expect($result['needs_reencryption'])->toBeFalse();
});