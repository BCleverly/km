<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Fantasy;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetFantasies
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $search = $request->get('search');

        $query = Fantasy::approved()
            ->with(['user', 'tags'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }

        // Filter premium content based on user subscription
        if (!$user->canAccessPremiumContent()) {
            $query->where('is_premium', false);
        }

        $fantasies = $query->limit($limit)->offset($offset)->get();

        return [
            'success' => true,
            'fantasies' => $fantasies->map(function ($fantasy) use ($user) {
                return [
                    'id' => $fantasy->id,
                    'content' => $fantasy->content,
                    'word_count' => $fantasy->word_count,
                    'is_premium' => $fantasy->is_premium,
                    'is_anonymous' => $fantasy->is_anonymous,
                    'view_count' => $fantasy->getViewCount(),
                    'created_at' => $fantasy->created_at,
                    'author' => $fantasy->is_anonymous ? null : [
                        'id' => $fantasy->user->id,
                        'name' => $fantasy->user->display_name,
                        'username' => $fantasy->user->profile?->username,
                    ],
                    'tags' => $fantasy->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                            'slug' => $tag->slug,
                        ];
                    }),
                    'reactions' => [
                        'count' => $fantasy->reactions()->count(),
                        'user_reacted' => $user->hasReactedTo($fantasy),
                    ],
                ];
            }),
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => $fantasies->count() === $limit,
            ],
        ];
    }
}