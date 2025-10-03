<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Status;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetStatuses
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $user_id = $request->get('user_id');

        $query = Status::public()
            ->with(['user.profile'])
            ->orderBy('created_at', 'desc');

        // Filter by specific user if provided
        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        $statuses = $query->limit($limit)->offset($offset)->get();

        return [
            'success' => true,
            'statuses' => $statuses->map(function ($status) use ($user) {
                return [
                    'id' => $status->id,
                    'content' => $status->content,
                    'is_public' => $status->is_public,
                    'has_image' => $status->hasImage(),
                    'status_image_url' => $status->status_image_url,
                    'created_at' => $status->created_at,
                    'user' => [
                        'id' => $status->user->id,
                        'name' => $status->user->display_name,
                        'username' => $status->user->profile?->username,
                        'profile_picture_url' => $status->user->profile_picture_url,
                    ],
                    'reactions' => [
                        'count' => $status->reactions()->count(),
                        'user_reacted' => $user->hasReactedTo($status),
                    ],
                    'comments' => [
                        'count' => $status->comments()->count(),
                    ],
                ];
            }),
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => $statuses->count() === $limit,
            ],
        ];
    }
}