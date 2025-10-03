<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Story;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetStory
{
    use AsAction;

    public function handle(Request $request, string $slug): array
    {
        $user = $request->user();

        $story = Story::approved()
            ->where('slug', $slug)
            ->with(['user', 'tags', 'comments.user.profile'])
            ->first();

        if (!$story) {
            return [
                'success' => false,
                'message' => 'Story not found',
            ];
        }

        // Track view
        app(\App\Services\ViewTrackingService::class)->trackView('story', $story->id);

        return [
            'success' => true,
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'slug' => $story->slug,
                'summary' => $story->summary,
                'content' => $story->content,
                'word_count' => $story->word_count,
                'reading_time_minutes' => $story->reading_time_minutes,
                'view_count' => $story->getViewCount(),
                'created_at' => $story->created_at,
                'updated_at' => $story->updated_at,
                'author' => [
                    'id' => $story->user->id,
                    'name' => $story->user->display_name,
                    'username' => $story->user->profile?->username,
                ],
                'tags' => $story->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                }),
                'reactions' => [
                    'count' => $story->reactions()->count(),
                    'user_reacted' => $user->hasReactedTo($story),
                ],
                'comments' => $story->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->display_name,
                            'username' => $comment->user->profile?->username,
                        ],
                    ];
                }),
            ],
        ];
    }
}