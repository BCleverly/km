# Encrypted Media System

This document explains how to use the encrypted media storage system built on top of Spatie Media Library.

## Overview

The encrypted media system provides secure storage for media files with the following features:

- **Encryption at Rest**: All files are encrypted using AES-256-CBC with your app key
- **Key Rotation Support**: Files can be re-encrypted when app keys change
- **Historical Key Support**: Can decrypt files encrypted with previous app keys
- **Queued Re-encryption**: Background jobs handle key rotation without blocking users
- **Seamless Integration**: Works with existing Spatie Media Library workflows

## Configuration

### 1. Environment Variables

Add historical keys to your `.env` file when rotating app keys:

```env
# Current app key
APP_KEY=base64:your-current-app-key

# Previous keys (comma-separated)
APP_PREVIOUS_KEYS=base64:old-key-1,base64:old-key-2
```

### 2. Filesystem Configuration

The encrypted disk is already configured in `config/filesystems.php`:

```php
'encrypted' => [
    'driver' => 'encrypted',
    'root' => storage_path('app/encrypted'),
    'throw' => false,
    'report' => false,
],
```

### 3. Media Library Configuration

The system uses a custom Media model that extends Spatie's Media model. This is configured in `config/media-library.php`:

```php
'media_model' => App\Models\Media::class,
```

## Usage

### Basic File Upload with Encryption

```php
use App\Models\User;
use Illuminate\Http\Request;

// Upload file to encrypted storage
$user = User::find(1);
$media = $user->addMediaFromRequest('file')
    ->toMediaCollection('documents', 'encrypted');

// The file is automatically encrypted and stored securely
```

### Manual File Storage

```php
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

// Create media record
$media = new Media();
$media->model_type = User::class;
$media->model_id = $user->id;
$media->collection_name = 'documents';
$media->name = 'document.pdf';
$media->file_name = 'document.pdf';
$media->mime_type = 'application/pdf';
$media->size = 1024;
$media->save();

// Store content to encrypted disk
$content = file_get_contents($filePath);
$media->storeToEncryptedDisk($content);
```

### Moving Existing Files to Encrypted Storage

```php
// Move from public disk to encrypted disk
$media = Media::find(1);
$media->moveToEncryptedDisk();
```

### Serving Encrypted Files

```php
// In your Blade templates or controllers
$media = Media::find(1);

// Get the URL for encrypted media (requires authentication)
$url = $media->getEncryptedUrl();

// Or use the route directly
$url = route('media.encrypted', $media);
```

### Checking Encryption Status

```php
$media = Media::find(1);

// Check if file is encrypted
if ($media->isEncrypted()) {
    // File is encrypted
}

// Check if file needs re-encryption
if ($media->needsReencryption()) {
    // File was encrypted with an old key
}
```

## Key Rotation

### When to Rotate Keys

Rotate your app key when:
- Security policies require regular key rotation
- A key compromise is suspected
- Moving to a new environment

### How to Rotate Keys

1. **Add Current Key to Historical Keys**:
   ```env
   APP_PREVIOUS_KEYS=base64:your-old-key
   ```

2. **Generate New App Key**:
   ```bash
   php artisan key:generate
   ```

3. **Re-encrypt All Files**:
   ```bash
   php artisan media:reencrypt
   ```

### Monitoring Re-encryption Progress

```bash
# Monitor queue workers
php artisan queue:work

# Check queue status
php artisan queue:monitor
```

## API Reference

### FileEncryptionService

```php
use App\Services\FileEncryptionService;

$service = app(FileEncryptionService::class);

// Encrypt content
$encrypted = $service->encrypt('plain text');

// Decrypt content
$decrypted = $service->decrypt($encrypted);

// Decrypt with fallback to historical keys
$result = $service->decryptWithFallback($encrypted);
// Returns: ['content' => string, 'key_id' => string, 'needs_reencryption' => bool]

// Check if content is encrypted
$isEncrypted = $service->isEncrypted($content);

// Get current key ID
$keyId = $service->getCurrentKeyId();
```

### Media Model Extensions

```php
use App\Models\Media;

$media = Media::find(1);

// Encryption metadata
$keyId = $media->getEncryptionKeyId();
$encryptedAt = $media->getEncryptedAt();
$needsReencryption = $media->needsReencryption();

// File operations
$content = $media->getEncryptedContent();
$result = $media->getEncryptedContentWithMetadata();
$media->storeToEncryptedDisk($content);
$media->moveToEncryptedDisk();

// Status checks
$isEncrypted = $media->isEncrypted();
$url = $media->getEncryptedUrl();
```

### EncryptedDisk

```php
use Illuminate\Support\Facades\Storage;

$disk = Storage::disk('encrypted');

// Basic operations
$disk->put('path/file.txt', 'content');
$content = $disk->get('path/file.txt');
$disk->delete('path/file.txt');

// With metadata
$result = $disk->getWithMetadata('path/file.txt');
// Returns: ['content' => string, 'key_id' => string, 'needs_reencryption' => bool]

// Stream operations
$stream = $disk->readStream('path/file.txt');
$disk->writeStream('path/file.txt', $stream);
```

## Security Considerations

### Key Management

- **Never commit keys to version control**
- **Use environment variables for key storage**
- **Consider using a key management service for production**
- **Regularly rotate keys according to security policies**

### File Access

- **All encrypted files require authentication**
- **Implement proper authorization in `EncryptedMediaController`**
- **Use HTTPS in production**
- **Consider additional access controls for sensitive files**

### Storage

- **Encrypted files are stored in `storage/app/encrypted/`**
- **Ensure proper file permissions on the storage directory**
- **Consider using encrypted storage volumes for additional security**

## Troubleshooting

### Common Issues

1. **"Failed to decrypt file content"**
   - Check if the file was encrypted with a different key
   - Verify historical keys are configured correctly
   - Ensure the file wasn't corrupted

2. **"Media file is not stored on encrypted disk"**
   - Check the media record's `disk` field
   - Use `moveToEncryptedDisk()` to migrate existing files

3. **Re-encryption jobs failing**
   - Check queue workers are running
   - Verify file permissions
   - Check application logs for detailed error messages

### Debugging

```php
// Check encryption status
$media = Media::find(1);
dd([
    'is_encrypted' => $media->isEncrypted(),
    'needs_reencryption' => $media->needsReencryption(),
    'key_id' => $media->getEncryptionKeyId(),
    'current_key_id' => app(FileEncryptionService::class)->getCurrentKeyId(),
]);

// Test encryption/decryption
$service = app(FileEncryptionService::class);
$testContent = 'test content';
$encrypted = $service->encrypt($testContent);
$decrypted = $service->decrypt($encrypted);
dd($testContent === $decrypted); // Should be true
```

## Performance Considerations

- **Re-encryption is done in background jobs** to avoid blocking users
- **Files are decrypted on-demand** when accessed
- **Consider caching decrypted content** for frequently accessed files
- **Monitor disk usage** as encrypted files may be larger than originals

## Testing

The system includes comprehensive tests:

```bash
# Run all encryption tests
php artisan test --filter=Encryption

# Run specific test suites
php artisan test tests/Unit/Services/FileEncryptionServiceTest.php
php artisan test tests/Unit/Filesystem/EncryptedDiskTest.php
php artisan test tests/Feature/Jobs/ReencryptMediaFilesTest.php
php artisan test tests/Feature/Models/MediaTest.php
php artisan test tests/Feature/Http/Controllers/EncryptedMediaControllerTest.php
```