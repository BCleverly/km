<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Story;
use App\ContentStatus;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetStories
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $search = $request->get('search');

        $query = Story::approved()
            ->with(['user', 'tags'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter premium content based on user subscription
        if (!$user->canAccessPremiumContent()) {
            // For now, we'll show all stories but mark premium ones
            // In a real implementation, you might want to filter these out
        }

        $stories = $query->limit($limit)->offset($offset)->get();

        return [
            'success' => true,
            'stories' => $stories->map(function ($story) use ($user) {
                return [
                    'id' => $story->id,
                    'title' => $story->title,
                    'slug' => $story->slug,
                    'summary' => $story->summary,
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
                ];
            }),
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => $stories->count() === $limit,
            ],
        ];
    }
}