<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Media;
use App\Services\FileEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EncryptedMediaController extends Controller
{
    public function __construct(
        private FileEncryptionService $encryptionService
    ) {}

    /**
     * Serve encrypted media file
     */
    public function show(Request $request, Media $media): Response|StreamedResponse
    {
        // Check if user has permission to view this media
        // You can add authorization logic here based on your requirements
        if (!$this->canViewMedia($request, $media)) {
            abort(403, 'Unauthorized to view this media');
        }

        try {
            // Get encrypted content with metadata
            $result = $media->getEncryptedContentWithMetadata();
            
            // If file needs re-encryption, dispatch job (but still serve the file)
            if ($result['needs_reencryption']) {
                \App\Jobs\ReencryptMediaFiles::dispatch($media->id, $result['key_id']);
            }

            // Create response with decrypted content
            $response = response($result['content'])
                ->header('Content-Type', $media->mime_type)
                ->header('Content-Length', strlen($result['content']))
                ->header('Cache-Control', 'private, max-age=3600')
                ->header('Content-Disposition', 'inline; filename="' . $media->file_name . '"');

            return $response;

        } catch (\Exception $e) {
            \Log::error("Failed to serve encrypted media {$media->id}: " . $e->getMessage());
            abort(500, 'Failed to retrieve media file');
        }
    }

    /**
     * Download encrypted media file
     */
    public function download(Request $request, Media $media): Response|StreamedResponse
    {
        // Check if user has permission to download this media
        if (!$this->canViewMedia($request, $media)) {
            abort(403, 'Unauthorized to download this media');
        }

        try {
            // Get encrypted content with metadata
            $result = $media->getEncryptedContentWithMetadata();
            
            // If file needs re-encryption, dispatch job (but still serve the file)
            if ($result['needs_reencryption']) {
                \App\Jobs\ReencryptMediaFiles::dispatch($media->id, $result['key_id']);
            }

            // Create download response
            $response = response($result['content'])
                ->header('Content-Type', $media->mime_type)
                ->header('Content-Length', strlen($result['content']))
                ->header('Cache-Control', 'private, no-cache')
                ->header('Content-Disposition', 'attachment; filename="' . $media->file_name . '"');

            return $response;

        } catch (\Exception $e) {
            \Log::error("Failed to download encrypted media {$media->id}: " . $e->getMessage());
            abort(500, 'Failed to download media file');
        }
    }

    /**
     * Check if user can view the media
     * Override this method based on your authorization requirements
     */
    protected function canViewMedia(Request $request, Media $media): bool
    {
        // Example: Check if user is authenticated
        if (!$request->user()) {
            return false;
        }

        // Example: Check if user owns the media or has permission
        // You can implement more complex authorization logic here
        if ($media->model_type === 'App\\Models\\User') {
            return $request->user()->id === $media->model_id;
        }

        // Add more authorization logic based on your requirements
        return true;
    }
}