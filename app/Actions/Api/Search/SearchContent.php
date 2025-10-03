<?php

declare(strict_types=1);

namespace App\Actions\Api\Search;

use App\Models\Story;
use App\Models\Fantasy;
use App\Models\Status;
use App\Models\Tasks\Task;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class SearchContent
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, stories, fantasies, statuses, tasks
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        if (empty($query)) {
            return [
                'success' => false,
                'message' => 'Search query is required',
            ];
        }

        $results = [];

        // Search stories
        if ($type === 'all' || $type === 'stories') {
            $stories = Story::approved()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('summary', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                })
                ->with(['user', 'tags'])
                ->limit($limit)
                ->offset($offset)
                ->get();

            $results['stories'] = $stories->map(function ($story) {
                return [
                    'id' => $story->id,
                    'title' => $story->title,
                    'slug' => $story->slug,
                    'summary' => $story->summary,
                    'word_count' => $story->word_count,
                    'created_at' => $story->created_at,
                    'author' => [
                        'id' => $story->user->id,
                        'name' => $story->user->display_name,
                        'username' => $story->user->profile?->username,
                    ],
                ];
            });
        }

        // Search fantasies
        if ($type === 'all' || $type === 'fantasies') {
            $fantasiesQuery = Fantasy::approved()
                ->where('content', 'like', "%{$query}%");

            // Filter premium content based on user subscription
            if (!$user->canAccessPremiumContent()) {
                $fantasiesQuery->where('is_premium', false);
            }

            $fantasies = $fantasiesQuery
                ->with(['user', 'tags'])
                ->limit($limit)
                ->offset($offset)
                ->get();

            $results['fantasies'] = $fantasies->map(function ($fantasy) {
                return [
                    'id' => $fantasy->id,
                    'content' => $fantasy->content,
                    'word_count' => $fantasy->word_count,
                    'is_premium' => $fantasy->is_premium,
                    'is_anonymous' => $fantasy->is_anonymous,
                    'created_at' => $fantasy->created_at,
                    'author' => $fantasy->is_anonymous ? null : [
                        'id' => $fantasy->user->id,
                        'name' => $fantasy->user->display_name,
                        'username' => $fantasy->user->profile?->username,
                    ],
                ];
            });
        }

        // Search statuses
        if ($type === 'all' || $type === 'statuses') {
            $statuses = Status::public()
                ->where('content', 'like', "%{$query}%")
                ->with(['user.profile'])
                ->limit($limit)
                ->offset($offset)
                ->get();

            $results['statuses'] = $statuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'content' => $status->content,
                    'has_image' => $status->hasImage(),
                    'created_at' => $status->created_at,
                    'user' => [
                        'id' => $status->user->id,
                        'name' => $status->user->display_name,
                        'username' => $status->user->profile?->username,
                    ],
                ];
            });
        }

        // Search tasks
        if ($type === 'all' || $type === 'tasks') {
            $tasks = Task::approved()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['author'])
                ->limit($limit)
                ->offset($offset)
                ->get();

            $results['tasks'] = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'difficulty_level' => $task->difficulty_level,
                    'duration_display' => $task->duration_display,
                    'target_user_type' => $task->target_user_type->value,
                    'target_user_type_label' => $task->target_user_type->label(),
                    'is_premium' => $task->is_premium,
                    'created_at' => $task->created_at,
                    'author' => [
                        'id' => $task->author->id,
                        'name' => $task->author->display_name,
                        'username' => $task->author->profile?->username,
                    ],
                ];
            });
        }

        return [
            'success' => true,
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
            ],
        ];
    }
}