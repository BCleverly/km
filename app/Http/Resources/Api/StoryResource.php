<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->when($request->routeIs('api.v1.content.stories.show'), $this->content),
            'word_count' => $this->word_count,
            'reading_time_minutes' => $this->reading_time_minutes,
            'view_count' => $this->getViewCount(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->display_name,
                'username' => $this->user->profile?->username,
            ],
            'tags' => $this->when($this->relationLoaded('tags'), function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                });
            }),
            'reactions' => [
                'count' => $this->reactions()->count(),
                'user_reacted' => $request->user()?->hasReactedTo($this) ?? false,
            ],
            'comments' => $this->when($request->routeIs('api.v1.content.stories.show') && $this->relationLoaded('comments'), function () {
                return $this->comments->map(function ($comment) {
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
                });
            }),
        ];
    }
}