<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Status;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateStatus
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        // Check daily status limit
        if ($user->hasReachedDailyStatusLimit()) {
            return [
                'success' => false,
                'message' => 'You have reached your daily status limit',
            ];
        }

        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:' . Status::getMaxLength()],
            'is_public' => ['boolean'],
            'status_image' => ['nullable', 'image', 'max:10240'], // 10MB max
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        $status = Status::create([
            'content' => $request->content,
            'user_id' => $user->id,
            'is_public' => $request->get('is_public', true),
        ]);

        // Handle image upload if provided
        if ($request->hasFile('status_image')) {
            $status->addMediaFromRequest('status_image')
                ->toMediaCollection('status_images');
        }

        return [
            'success' => true,
            'message' => 'Status created successfully',
            'status' => [
                'id' => $status->id,
                'content' => $status->content,
                'is_public' => $status->is_public,
                'has_image' => $status->hasImage(),
                'status_image_url' => $status->status_image_url,
                'created_at' => $status->created_at,
            ],
        ];
    }
}