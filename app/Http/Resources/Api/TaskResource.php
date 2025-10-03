<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'assigned_at' => $this->assigned_at,
            'completed_at' => $this->completed_at,
            'failed_at' => $this->failed_at,
            'deadline' => $this->deadline,
            'has_completion_image' => $this->has_completion_image,
            'completion_note' => $this->completion_note,
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'description' => $this->task->description,
                'difficulty_level' => $this->task->difficulty_level,
                'duration_display' => $this->task->duration_display,
                'target_user_type' => $this->task->target_user_type->value,
                'target_user_type_label' => $this->task->target_user_type->label(),
                'is_premium' => $this->task->is_premium,
                'view_count' => $this->task->view_count,
                'author' => [
                    'id' => $this->task->author->id,
                    'name' => $this->task->author->display_name,
                    'username' => $this->task->author->profile?->username,
                ],
            ],
            'potential_reward' => $this->when($this->potentialReward, function () {
                return [
                    'id' => $this->potentialReward->id,
                    'title' => $this->potentialReward->title,
                    'description' => $this->potentialReward->description,
                    'difficulty_level' => $this->potentialReward->difficulty_level,
                ];
            }),
            'potential_punishment' => $this->when($this->potentialPunishment, function () {
                return [
                    'id' => $this->potentialPunishment->id,
                    'title' => $this->potentialPunishment->title,
                    'description' => $this->potentialPunishment->description,
                    'difficulty_level' => $this->potentialPunishment->difficulty_level,
                ];
            }),
        ];
    }
}