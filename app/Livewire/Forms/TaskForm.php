<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\ContentStatus;
use App\TargetUserType;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskForm extends Form
{
    // Task Selection
    public string $taskSelection = 'existing'; // 'existing' or 'create'
    public ?int $selectedTaskId = null;
    public string $taskSearch = '';

    // Custom Task Creation
    #[Validate('required_if:taskSelection,create|string|min:3|max:255')]
    public string $title = '';

    #[Validate('required_if:taskSelection,create|string|min:10|max:2000')]
    public string $description = '';

    #[Validate('required_if:taskSelection,create|integer|min:1|max:5')]
    public int $difficultyLevel = 3;

    #[Validate('required_if:taskSelection,create|integer|min:1|max:999')]
    public int $durationTime = 1;

    #[Validate('required_if:taskSelection,create|in:minutes,hours,days,weeks')]
    public string $durationType = 'hours';

    #[Validate('required_if:taskSelection,create')]
    public TargetUserType $targetUserType = TargetUserType::Any;

    public bool $isPremium = false;

    // Reward Selection
    public string $rewardSelection = 'existing'; // 'existing' or 'create'
    public ?int $selectedRewardId = null;
    public string $rewardSearch = '';

    // Custom Reward Creation
    #[Validate('required_if:rewardSelection,create|string|min:3|max:255')]
    public string $rewardTitle = '';

    #[Validate('required_if:rewardSelection,create|string|min:10|max:2000')]
    public string $rewardDescription = '';

    #[Validate('required_if:rewardSelection,create|integer|min:1|max:5')]
    public int $rewardDifficultyLevel = 3;

    // Punishment Selection
    public string $punishmentSelection = 'existing'; // 'existing' or 'create'
    public ?int $selectedPunishmentId = null;
    public string $punishmentSearch = '';

    // Custom Punishment Creation
    #[Validate('required_if:punishmentSelection,create|string|min:3|max:255')]
    public string $punishmentTitle = '';

    #[Validate('required_if:punishmentSelection,create|string|min:10|max:2000')]
    public string $punishmentDescription = '';

    #[Validate('required_if:punishmentSelection,create|integer|min:1|max:5')]
    public int $punishmentDifficultyLevel = 3;

    // Privacy/Review Options
    public bool $keepPrivate = true;

    public function rules(): array
    {
        return [
            'taskSelection' => 'required|in:existing,create',
            'selectedTaskId' => 'required_if:taskSelection,existing|nullable|integer|exists:tasks,id',
            'title' => 'required_if:taskSelection,create|string|min:3|max:255',
            'description' => 'required_if:taskSelection,create|string|min:10|max:2000',
            'difficultyLevel' => 'required_if:taskSelection,create|integer|min:1|max:5',
            'durationTime' => 'required_if:taskSelection,create|integer|min:1|max:999',
            'durationType' => 'required_if:taskSelection,create|in:minutes,hours,days,weeks',
            'targetUserType' => 'required_if:taskSelection,create',
            'isPremium' => 'boolean',
            'rewardSelection' => 'required|in:existing,create',
            'selectedRewardId' => 'required_if:rewardSelection,existing|nullable|integer|exists:outcomes,id',
            'rewardTitle' => 'required_if:rewardSelection,create|string|min:3|max:255',
            'rewardDescription' => 'required_if:rewardSelection,create|string|min:10|max:2000',
            'rewardDifficultyLevel' => 'required_if:rewardSelection,create|integer|min:1|max:5',
            'punishmentSelection' => 'required|in:existing,create',
            'selectedPunishmentId' => 'required_if:punishmentSelection,existing|nullable|integer|exists:outcomes,id',
            'punishmentTitle' => 'required_if:punishmentSelection,create|string|min:3|max:255',
            'punishmentDescription' => 'required_if:punishmentSelection,create|string|min:10|max:2000',
            'punishmentDifficultyLevel' => 'required_if:punishmentSelection,create|integer|min:1|max:5',
            'keepPrivate' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'taskSelection.required' => 'Please choose whether to use an existing task or create a new one.',
            'selectedTaskId.required_if' => 'Please select an existing task.',
            'selectedTaskId.exists' => 'The selected task does not exist.',
            'title.required_if' => 'Task title is required when creating a new task.',
            'title.min' => 'Task title must be at least 3 characters.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.required_if' => 'Task description is required when creating a new task.',
            'description.min' => 'Task description must be at least 10 characters.',
            'description.max' => 'Task description cannot exceed 2000 characters.',
            'difficultyLevel.required_if' => 'Difficulty level is required when creating a new task.',
            'difficultyLevel.min' => 'Difficulty level must be at least 1.',
            'difficultyLevel.max' => 'Difficulty level cannot exceed 5.',
            'durationTime.required_if' => 'Duration time is required when creating a new task.',
            'durationTime.min' => 'Duration time must be at least 1.',
            'durationTime.max' => 'Duration time cannot exceed 999.',
            'durationType.required_if' => 'Duration type is required when creating a new task.',
            'durationType.in' => 'Duration type must be one of: minutes, hours, days, weeks.',
            'targetUserType.required_if' => 'Target user type is required when creating a new task.',
            'rewardSelection.required' => 'Please choose whether to use an existing reward or create a new one.',
            'selectedRewardId.required_if' => 'Please select an existing reward.',
            'selectedRewardId.exists' => 'The selected reward does not exist.',
            'rewardTitle.required_if' => 'Reward title is required when creating a new reward.',
            'rewardTitle.min' => 'Reward title must be at least 3 characters.',
            'rewardTitle.max' => 'Reward title cannot exceed 255 characters.',
            'rewardDescription.required_if' => 'Reward description is required when creating a new reward.',
            'rewardDescription.min' => 'Reward description must be at least 10 characters.',
            'rewardDescription.max' => 'Reward description cannot exceed 2000 characters.',
            'rewardDifficultyLevel.required_if' => 'Reward difficulty level is required when creating a new reward.',
            'rewardDifficultyLevel.min' => 'Reward difficulty level must be at least 1.',
            'rewardDifficultyLevel.max' => 'Reward difficulty level cannot exceed 5.',
            'punishmentSelection.required' => 'Please choose whether to use an existing punishment or create a new one.',
            'selectedPunishmentId.required_if' => 'Please select an existing punishment.',
            'selectedPunishmentId.exists' => 'The selected punishment does not exist.',
            'punishmentTitle.required_if' => 'Punishment title is required when creating a new punishment.',
            'punishmentTitle.min' => 'Punishment title must be at least 3 characters.',
            'punishmentTitle.max' => 'Punishment title cannot exceed 255 characters.',
            'punishmentDescription.required_if' => 'Punishment description is required when creating a new punishment.',
            'punishmentDescription.min' => 'Punishment description must be at least 10 characters.',
            'punishmentDescription.max' => 'Punishment description cannot exceed 2000 characters.',
            'punishmentDifficultyLevel.required_if' => 'Punishment difficulty level is required when creating a new punishment.',
            'punishmentDifficultyLevel.min' => 'Punishment difficulty level must be at least 1.',
            'punishmentDifficultyLevel.max' => 'Punishment difficulty level cannot exceed 5.',
        ];
    }
}
