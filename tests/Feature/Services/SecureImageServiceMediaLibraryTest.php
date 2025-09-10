<?php

use App\Models\Profile;
use App\Models\User;
use App\Services\SecureImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

it('can add secure image to media library model', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);
    
    $media = $service->addSecureImageToMedia($profile, $file, 'profile_pictures');
    
    expect($media)->not->toBeNull();
    expect($media->model_type)->toBe(Profile::class);
    expect($media->model_id)->toBe($profile->id);
    expect($media->collection_name)->toBe('profile_pictures');
    expect($media->custom_properties['is_encrypted'])->toBeTrue();
    expect($media->custom_properties['original_name'])->toBe('profile.jpg');
    
    // Verify encrypted file exists
    expect(Storage::disk('public')->exists($media->custom_properties['encrypted_path']))->toBeTrue();
});

it('can add secure image from content to media library model', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    // Create fake image content
    $content = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xFF\xD9";
    
    $media = $service->addSecureImageFromContentToMedia($profile, $content, 'profile.jpg', 'image/jpeg', 'profile_pictures');
    
    expect($media)->not->toBeNull();
    expect($media->model_type)->toBe(Profile::class);
    expect($media->model_id)->toBe($profile->id);
    expect($media->collection_name)->toBe('profile_pictures');
    expect($media->custom_properties['is_encrypted'])->toBeTrue();
    expect($media->custom_properties['original_name'])->toBe('profile.jpg');
    
    // Verify encrypted file exists
    expect(Storage::disk('public')->exists($media->custom_properties['encrypted_path']))->toBeTrue();
});

it('can retrieve secure image from media library', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);
    $media = $service->addSecureImageToMedia($profile, $file, 'profile_pictures');
    
    $result = $service->getSecureImageFromMedia($media);
    
    expect($result['success'])->toBeTrue();
    expect($result['content'])->not->toBeNull();
    expect($result['mime_type'])->toBe('image/jpeg');
    expect($result['original_name'])->toBe('profile.jpg');
});

it('can check if media is encrypted', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);
    $media = $service->addSecureImageToMedia($profile, $file, 'profile_pictures');
    
    expect($service->isMediaEncrypted($media))->toBeTrue();
    
    // Test with non-encrypted media
    $regularMedia = $profile->addMedia(UploadedFile::fake()->image('regular.jpg', 200, 200))
        ->toMediaCollection('profile_pictures');
    
    expect($service->isMediaEncrypted($regularMedia))->toBeFalse();
});

it('can delete secure image from media library', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);
    $media = $service->addSecureImageToMedia($profile, $file, 'profile_pictures');
    
    $encryptedPath = $media->custom_properties['encrypted_path'];
    expect(Storage::disk('public')->exists($encryptedPath))->toBeTrue();
    
    $result = $service->deleteSecureImageFromMedia($media);
    
    expect($result)->toBeTrue();
    expect(Storage::disk('public')->exists($encryptedPath))->toBeFalse();
    expect(Media::find($media->id))->toBeNull();
});

it('can create secure media collection configuration', function () {
    $service = app(SecureImageService::class);
    
    $collection = $service->createSecureMediaCollection('test_collection', ['image/jpeg', 'image/png'], true);
    
    expect($collection['name'])->toBe('test_collection');
    expect($collection['mime_types'])->toContain('image/jpeg');
    expect($collection['mime_types'])->toContain('image/png');
    expect($collection['single_file'])->toBeTrue();
    expect($collection['is_secure'])->toBeTrue();
});

it('can register secure media collections for model', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    $service = app(SecureImageService::class);
    
    $collections = [
        $service->createSecureMediaCollection('test_collection', ['image/jpeg'], false),
    ];
    
    $service->registerSecureMediaCollections($profile, $collections);
    
    // Verify collection was registered
    $registeredCollections = $profile->getMediaCollections();
    expect($registeredCollections)->toHaveCount(3); // profile_pictures, cover_photos, test_collection
});