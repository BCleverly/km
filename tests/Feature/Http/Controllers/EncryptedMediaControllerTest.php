<?php

declare(strict_types=1);

use App\Http\Controllers\EncryptedMediaController;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('encrypted');
    Storage::fake('public');
});

it('can serve encrypted media file', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    $testContent = 'This is test image content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $response = $this->actingAs($user)
        ->get(route('media.encrypted', $media));
    
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
    $response->assertHeader('Content-Disposition', 'inline; filename="test-image.jpg"');
    expect($response->getContent())->toBe($testContent);
});

it('can download encrypted media file', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-document.pdf',
        'mime_type' => 'application/pdf',
        'size' => 2048,
    ]);
    
    $testContent = 'This is test PDF content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $response = $this->actingAs($user)
        ->get(route('media.download', $media));
    
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename="test-document.pdf"');
    expect($response->getContent())->toBe($testContent);
});

it('requires authentication to view media', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    $response = $this->get(route('media.encrypted', $media));
    
    $response->assertRedirect(route('login'));
});

it('prevents unauthorized access to other users media', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $owner->id,
        'disk' => 'encrypted',
        'file_name' => 'private-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    $testContent = 'This is private image content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $response = $this->actingAs($otherUser)
        ->get(route('media.encrypted', $media));
    
    $response->assertStatus(403);
});

it('handles missing media file gracefully', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'missing-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Don't create the actual file on disk
    
    $response = $this->actingAs($user)
        ->get(route('media.encrypted', $media));
    
    $response->assertStatus(500);
});

it('handles decryption errors gracefully', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'corrupted-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    // Store invalid encrypted content
    Storage::disk('encrypted')->put($media->getPath(), 'invalid-encrypted-content');
    
    $response = $this->actingAs($user)
        ->get(route('media.encrypted', $media));
    
    $response->assertStatus(500);
});

it('sets appropriate cache headers for media files', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-image.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    
    $testContent = 'This is test image content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $response = $this->actingAs($user)
        ->get(route('media.encrypted', $media));
    
    $response->assertStatus(200);
    $response->assertHeader('Cache-Control', 'private, max-age=3600');
});

it('sets no-cache headers for downloads', function () {
    $user = User::factory()->create();
    $media = Media::factory()->create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'disk' => 'encrypted',
        'file_name' => 'test-document.pdf',
        'mime_type' => 'application/pdf',
        'size' => 2048,
    ]);
    
    $testContent = 'This is test PDF content.';
    Storage::disk('encrypted')->put($media->getPath(), $testContent);
    
    $response = $this->actingAs($user)
        ->get(route('media.download', $media));
    
    $response->assertStatus(200);
    $response->assertHeader('Cache-Control', 'private, no-cache');
});