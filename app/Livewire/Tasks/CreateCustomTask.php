<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\CreateCustomTask as CreateCustomTaskAction;
use App\ContentStatus;
use App\Livewire\Forms\TaskForm;
use App\TargetUserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateCustomTask extends Component
{
    public TaskForm $form;

    public bool $isSubmitting = false;
    public ?string $successMessage = null;

    public function updatedFormIncludeReward(): void
    {
        if (!$this->form->includeReward) {
            $this->form->rewardTitle = '';
            $this->form->rewardDescription = '';
            $this->form->rewardDifficultyLevel = 3;
        }
    }

    public function updatedFormIncludePunishment(): void
    {
        if (!$this->form->includePunishment) {
            $this->form->punishmentTitle = '';
            $this->form->punishmentDescription = '';
            $this->form->punishmentDifficultyLevel = 3;
        }
    }

    public function updatedFormDifficultyLevel(): void
    {
        // Sync reward and punishment difficulty levels with task difficulty
        if (!$this->form->includeReward) {
            $this->form->rewardDifficultyLevel = $this->form->difficultyLevel;
        }
        if (!$this->form->includePunishment) {
            $this->form->punishmentDifficultyLevel = $this->form->difficultyLevel;
        }
    }

    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->successMessage = null;

        // Validate the form - this will automatically show inline errors
        $this->form->validate();

        $user = Auth::user();
        if (!$user) {
            $this->addError('form', 'You must be logged in to create a custom task.');
            $this->isSubmitting = false;
            return;
        }

        try {
            $result = CreateCustomTaskAction::run(
                user: $user,
                title: $this->form->title,
                description: $this->form->description,
                difficultyLevel: $this->form->difficultyLevel,
                durationTime: $this->form->durationTime,
                durationType: $this->form->durationType,
                targetUserType: $this->form->targetUserType,
                isPremium: $this->form->isPremium,
                rewardTitle: $this->form->includeReward ? $this->form->rewardTitle : null,
                rewardDescription: $this->form->includeReward ? $this->form->rewardDescription : null,
                rewardDifficultyLevel: $this->form->includeReward ? $this->form->rewardDifficultyLevel : null,
                punishmentTitle: $this->form->includePunishment ? $this->form->punishmentTitle : null,
                punishmentDescription: $this->form->includePunishment ? $this->form->punishmentDescription : null,
                punishmentDifficultyLevel: $this->form->includePunishment ? $this->form->punishmentDifficultyLevel : null,
            );

            $this->successMessage = 'Your custom task has been created successfully! It will be reviewed before being made available to other users.';
            
            // Reset form
            $this->form->reset();

        } catch (\Exception $e) {
            $this->addError('form', $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function resetForm(): void
    {
        $this->form->reset();
    }

    public function getTargetUserTypeOptions(): array
    {
        return collect(TargetUserType::cases())->mapWithKeys(function (TargetUserType $type) {
            return [$type->value => $type->label()];
        })->toArray();
    }

    public function getDurationTypeOptions(): array
    {
        return [
            'minutes' => 'Minutes',
            'hours' => 'Hours',
            'days' => 'Days',
            'weeks' => 'Weeks',
        ];
    }

    public function getDifficultyLevelOptions(): array
    {
        return [
            1 => '1 - Very Easy',
            2 => '2 - Easy',
            3 => '3 - Medium',
            4 => '4 - Hard',
            5 => '5 - Very Hard',
        ];
    }

    public function render()
    {
        return view('livewire.tasks.create-custom-task');
    }

}
