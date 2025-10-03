<?php

declare(strict_types=1);

namespace App\Actions\Api\Tasks;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetActiveTask
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $taskService = $user->tasks();
        $activeTask = $taskService->getActiveTask();

        if (!$activeTask) {
            return [
                'success' => true,
                'has_active_task' => false,
                'message' => 'No active task',
                'task' => null,
            ];
        }

        return [
            'success' => true,
            'has_active_task' => true,
            'task' => [
                'id' => $activeTask->id,
                'status' => $activeTask->status->value,
                'status_label' => $activeTask->status->label(),
                'assigned_at' => $activeTask->assigned_at,
                'deadline' => $activeTask->deadline,
                'task' => [
                    'id' => $activeTask->task->id,
                    'title' => $activeTask->task->title,
                    'description' => $activeTask->task->description,
                    'difficulty_level' => $activeTask->task->difficulty_level,
                    'duration_display' => $activeTask->task->duration_display,
                    'target_user_type' => $activeTask->task->target_user_type->value,
                    'target_user_type_label' => $activeTask->task->target_user_type->label(),
                    'is_premium' => $activeTask->task->is_premium,
                    'view_count' => $activeTask->task->view_count,
                    'author' => [
                        'id' => $activeTask->task->author->id,
                        'name' => $activeTask->task->author->display_name,
                        'username' => $activeTask->task->author->profile?->username,
                    ],
                ],
                'potential_reward' => $activeTask->potentialReward ? [
                    'id' => $activeTask->potentialReward->id,
                    'title' => $activeTask->potentialReward->title,
                    'description' => $activeTask->potentialReward->description,
                    'difficulty_level' => $activeTask->potentialReward->difficulty_level,
                ] : null,
                'potential_punishment' => $activeTask->potentialPunishment ? [
                    'id' => $activeTask->potentialPunishment->id,
                    'title' => $activeTask->potentialPunishment->title,
                    'description' => $activeTask->potentialPunishment->description,
                    'difficulty_level' => $activeTask->potentialPunishment->difficulty_level,
                ] : null,
            ],
        ];
    }
}