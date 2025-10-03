<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Story;
use App\ContentStatus;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateStory
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        // Check if user can create stories
        if (!$user->canCreateStories()) {
            return [
                'success' => false,
                'message' => 'Story creation is not available on your current plan',
            ];
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string', 'min:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        $story = Story::create([
            'title' => $request->title,
            'summary' => $request->summary,
            'content' => $request->content,
            'user_id' => $user->id,
            'status' => ContentStatus::Pending,
        ]);

        // Attach tags if provided
        if ($request->has('tags') && is_array($request->tags)) {
            $story->syncTags($request->tags);
        }

        return [
            'success' => true,
            'message' => 'Story created successfully and submitted for review',
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'slug' => $story->slug,
                'summary' => $story->summary,
                'status' => $story->status->value,
                'status_label' => $story->status_label,
                'created_at' => $story->created_at,
            ],
        ];
    }
}