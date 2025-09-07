<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\TargetUserType;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskForm extends Form
{
    // Task fields
    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $description = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $difficultyLevel = 3;

    #[Validate('required|integer|min:1|max:999')]
    public int $durationTime = 1;

    #[Validate('required|in:minutes,hours,days,weeks')]
    public string $durationType = 'hours';

    #[Validate('required')]
    public TargetUserType $targetUserType = TargetUserType::Any;

    public bool $isPremium = false;

    // Reward fields
    public bool $includeReward = false;

    #[Validate('required_if:includeReward,true|string|min:3|max:255')]
    public string $rewardTitle = '';

    #[Validate('required_if:includeReward,true|string|min:10|max:2000')]
    public string $rewardDescription = '';

    #[Validate('required_if:includeReward,true|integer|min:1|max:5')]
    public int $rewardDifficultyLevel = 3;

    // Punishment fields
    public bool $includePunishment = false;

    #[Validate('required_if:includePunishment,true|string|min:3|max:255')]
    public string $punishmentTitle = '';

    #[Validate('required_if:includePunishment,true|string|min:10|max:2000')]
    public string $punishmentDescription = '';

    #[Validate('required_if:includePunishment,true|integer|min:1|max:5')]
    public int $punishmentDifficultyLevel = 3;

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:2000',
            'difficultyLevel' => 'required|integer|min:1|max:5',
            'durationTime' => 'required|integer|min:1|max:999',
            'durationType' => 'required|in:minutes,hours,days,weeks',
            'targetUserType' => 'required',
            'isPremium' => 'boolean',
            'includeReward' => 'boolean',
            'rewardTitle' => 'required_if:includeReward,true|string|min:3|max:255',
            'rewardDescription' => 'required_if:includeReward,true|string|min:10|max:2000',
            'rewardDifficultyLevel' => 'required_if:includeReward,true|integer|min:1|max:5',
            'includePunishment' => 'boolean',
            'punishmentTitle' => 'required_if:includePunishment,true|string|min:3|max:255',
            'punishmentDescription' => 'required_if:includePunishment,true|string|min:10|max:2000',
            'punishmentDifficultyLevel' => 'required_if:includePunishment,true|integer|min:1|max:5',
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
            'difficultyLevel.max' => 'Difficulty level cannot exceed 5.',
            'durationTime.required' => 'Duration time is required.',
            'durationTime.min' => 'Duration time must be at least 1.',
            'durationTime.max' => 'Duration time cannot exceed 999.',
            'durationType.required' => 'Duration type is required.',
            'durationType.in' => 'Duration type must be one of: minutes, hours, days, weeks.',
            'targetUserType.required' => 'Target user type is required.',
            'rewardTitle.required_if' => 'Reward title is required when including a reward.',
            'rewardTitle.min' => 'Reward title must be at least 3 characters.',
            'rewardTitle.max' => 'Reward title cannot exceed 255 characters.',
            'rewardDescription.required_if' => 'Reward description is required when including a reward.',
            'rewardDescription.min' => 'Reward description must be at least 10 characters.',
            'rewardDescription.max' => 'Reward description cannot exceed 2000 characters.',
            'rewardDifficultyLevel.required_if' => 'Reward difficulty level is required when including a reward.',
            'rewardDifficultyLevel.min' => 'Reward difficulty level must be at least 1.',
            'rewardDifficultyLevel.max' => 'Reward difficulty level cannot exceed 5.',
            'punishmentTitle.required_if' => 'Punishment title is required when including a punishment.',
            'punishmentTitle.min' => 'Punishment title must be at least 3 characters.',
            'punishmentTitle.max' => 'Punishment title cannot exceed 255 characters.',
            'punishmentDescription.required_if' => 'Punishment description is required when including a punishment.',
            'punishmentDescription.min' => 'Punishment description must be at least 10 characters.',
            'punishmentDescription.max' => 'Punishment description cannot exceed 2000 characters.',
            'punishmentDifficultyLevel.required_if' => 'Punishment difficulty level is required when including a punishment.',
            'punishmentDifficultyLevel.min' => 'Punishment difficulty level must be at least 1.',
            'punishmentDifficultyLevel.max' => 'Punishment difficulty level cannot exceed 5.',
        ];
    }
}
