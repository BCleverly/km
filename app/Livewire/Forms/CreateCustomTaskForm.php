<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\ContentStatus;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\TargetUserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateCustomTaskForm extends Form
{
    // Task Details
    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $description = '';

    #[Validate('required|integer|min:1|max:6')]
    public int $difficultyLevel = 3;

    #[Validate('required|integer|min:1|max:999')]
    public int $durationTime = 1;

    #[Validate('required|in:minutes,hours,days,weeks')]
    public string $durationType = 'hours';

    #[Validate('required')]
    public TargetUserType $targetUserType = TargetUserType::Any;

    public bool $isPremium = false;

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:2000',
            'difficultyLevel' => 'required|integer|min:1|max:6',
            'durationTime' => 'required|integer|min:1|max:999',
            'durationType' => 'required|in:minutes,hours,days,weeks',
            'targetUserType' => 'required',
            'isPremium' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.min' => 'Task title must be at least 3 characters.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.required' => 'Task description is required.',
            'description.min' => 'Task description must be at least 10 characters.',
            'description.max' => 'Task description cannot exceed 2000 characters.',
            'difficultyLevel.required' => 'Difficulty level is required.',
            'difficultyLevel.min' => 'Difficulty level must be at least 1.',
            'difficultyLevel.max' => 'Difficulty level cannot exceed 6.',
            'durationTime.required' => 'Duration time is required.',
            'durationTime.min' => 'Duration time must be at least 1.',
            'durationTime.max' => 'Duration time cannot exceed 999.',
            'durationType.required' => 'Duration type is required.',
            'durationType.in' => 'Duration type must be one of: minutes, hours, days, weeks.',
            'targetUserType.required' => 'Target user type is required.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            throw new \Exception('You must be logged in to submit a task.');
        }

        // Create the task
        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'difficulty_level' => $this->difficultyLevel,
            'duration_time' => $this->durationTime,
            'duration_type' => $this->durationType,
            'target_user_type' => $this->targetUserType,
            'user_id' => $user->id,
            'status' => ContentStatus::Pending,
            'is_premium' => $this->isPremium,
        ]);
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->difficultyLevel = 3;
        $this->durationTime = 1;
        $this->durationType = 'hours';
        $this->targetUserType = TargetUserType::Any;
        $this->isPremium = false;
    }
}
