<?php

declare(strict_types=1);

namespace App\Actions\Api\Tasks;

use App\Actions\Tasks\CompleteTask as CompleteTaskAction;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompleteTask
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'completion_note' => ['nullable', 'string', 'max:1000'],
            'completion_image' => ['nullable', 'image', 'max:10240'], // 10MB max
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Check if user has an active task
        if (!$user->tasks()->hasActiveTask()) {
            return [
                'success' => false,
                'message' => 'No active task to complete',
            ];
        }

        // Check if user can upload images (premium feature)
        $completionImage = null;
        if ($request->hasFile('completion_image')) {
            if (!$user->canUploadCompletionImages()) {
                return [
                    'success' => false,
                    'message' => 'Image uploads are not available on your current plan',
                ];
            }
            $completionImage = $request->file('completion_image');
        }

        $result = CompleteTaskAction::run(
            user: $user,
            completionImage: $completionImage,
            completionNote: $request->completion_note
        );

        return [
            'success' => $result['success'],
            'message' => $result['message'],
            'task' => $result['task'] ? [
                'id' => $result['task']->id,
                'status' => $result['task']->status->value,
                'status_label' => $result['task']->status->label(),
                'completed_at' => $result['task']->completed_at,
                'has_completion_image' => $result['task']->has_completion_image,
                'completion_note' => $result['task']->completion_note,
                'task' => [
                    'id' => $result['task']->task->id,
                    'title' => $result['task']->task->title,
                    'description' => $result['task']->task->description,
                ],
                'outcome' => $result['task']->outcome ? [
                    'id' => $result['task']->outcome->id,
                    'title' => $result['task']->outcome->title,
                    'description' => $result['task']->outcome->description,
                    'type' => $result['task']->outcome_type,
                ] : null,
            ] : null,
        ];
    }
}