<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\ContentStatus;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateCustomOutcomeForm extends Form
{
    // Outcome fields
    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $description = '';

    #[Validate('required|integer|min:1|max:6')]
    public int $difficultyLevel = 3;

    #[Validate('required')]
    public TargetUserType $targetUserType = TargetUserType::Any;

    #[Validate('required|in:reward,punishment')]
    public string $intendedType = 'reward';

    public bool $isPremium = false;

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:2000',
            'difficultyLevel' => 'required|integer|min:1|max:6',
            'targetUserType' => 'required',
            'intendedType' => 'required|in:reward,punishment',
            'isPremium' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Outcome title is required.',
            'title.min' => 'Outcome title must be at least 3 characters.',
            'title.max' => 'Outcome title cannot exceed 255 characters.',
            'description.required' => 'Outcome description is required.',
            'description.min' => 'Outcome description must be at least 10 characters.',
            'description.max' => 'Outcome description cannot exceed 2000 characters.',
            'difficultyLevel.required' => 'Difficulty level is required.',
            'difficultyLevel.min' => 'Difficulty level must be at least 1.',
            'difficultyLevel.max' => 'Difficulty level cannot exceed 6.',
            'targetUserType.required' => 'Target user type is required.',
            'intendedType.required' => 'Intended type is required.',
            'intendedType.in' => 'Intended type must be either reward or punishment.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            throw new \Exception('You must be logged in to submit an outcome.');
        }

        // Create the outcome
        Outcome::create([
            'title' => $this->title,
            'description' => $this->description,
            'difficulty_level' => $this->difficultyLevel,
            'target_user_type' => $this->targetUserType,
            'user_id' => $user->id,
            'status' => ContentStatus::Pending,
            'intended_type' => $this->intendedType,
            'is_premium' => $this->isPremium,
        ]);
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->difficultyLevel = 3;
        $this->targetUserType = TargetUserType::Any;
        $this->intendedType = 'reward';
        $this->isPremium = false;
    }
}
