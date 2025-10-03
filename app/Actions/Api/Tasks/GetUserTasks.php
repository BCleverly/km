<?php

declare(strict_types=1);

namespace App\Actions\Api\Tasks;

use App\Models\User;
use App\TaskStatus;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetUserTasks
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $status = $request->get('status', 'all');
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $query = $user->assignedTasks()
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->orderBy('assigned_at', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $statusEnum = match ($status) {
                'assigned' => TaskStatus::Assigned,
                'completed' => TaskStatus::Completed,
                'failed' => TaskStatus::Failed,
                default => null,
            };

            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        $tasks = $query->limit($limit)->offset($offset)->get();

        return [
            'success' => true,
            'tasks' => $tasks->map(function ($assignedTask) {
                return [
                    'id' => $assignedTask->id,
                    'status' => $assignedTask->status->value,
                    'status_label' => $assignedTask->status->label(),
                    'assigned_at' => $assignedTask->assigned_at,
                    'completed_at' => $assignedTask->completed_at,
                    'failed_at' => $assignedTask->failed_at,
                    'deadline' => $assignedTask->deadline,
                    'has_completion_image' => $assignedTask->has_completion_image,
                    'completion_note' => $assignedTask->completion_note,
                    'task' => [
                        'id' => $assignedTask->task->id,
                        'title' => $assignedTask->task->title,
                        'description' => $assignedTask->task->description,
                        'difficulty_level' => $assignedTask->task->difficulty_level,
                        'duration_display' => $assignedTask->task->duration_display,
                        'target_user_type' => $assignedTask->task->target_user_type->value,
                        'target_user_type_label' => $assignedTask->task->target_user_type->label(),
                        'is_premium' => $assignedTask->task->is_premium,
                        'view_count' => $assignedTask->task->view_count,
                        'author' => [
                            'id' => $assignedTask->task->author->id,
                            'name' => $assignedTask->task->author->display_name,
                            'username' => $assignedTask->task->author->profile?->username,
                        ],
                    ],
                    'potential_reward' => $assignedTask->potentialReward ? [
                        'id' => $assignedTask->potentialReward->id,
                        'title' => $assignedTask->potentialReward->title,
                        'description' => $assignedTask->potentialReward->description,
                        'difficulty_level' => $assignedTask->potentialReward->difficulty_level,
                    ] : null,
                    'potential_punishment' => $assignedTask->potentialPunishment ? [
                        'id' => $assignedTask->potentialPunishment->id,
                        'title' => $assignedTask->potentialPunishment->title,
                        'description' => $assignedTask->potentialPunishment->description,
                        'difficulty_level' => $assignedTask->potentialPunishment->difficulty_level,
                    ] : null,
                ];
            }),
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => $tasks->count() === $limit,
            ],
        ];
    }
}