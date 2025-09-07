<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\CreateCustomTask as CreateCustomTaskAction;
use App\ContentStatus;
use App\Livewire\Forms\TaskForm;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateCustomTask extends Component
{
    public TaskForm $form;

    public bool $isSubmitting = false;
    public ?string $successMessage = null;

    public function updatedFormTaskSelection(): void
    {
        if ($this->form->taskSelection === 'existing') {
            $this->form->title = '';
            $this->form->description = '';
            $this->form->difficultyLevel = 3;
            $this->form->durationTime = 1;
            $this->form->durationType = 'hours';
            $this->form->targetUserType = TargetUserType::Any;
            $this->form->isPremium = false;
        }
    }

    public function updatedFormTaskSearch(): void
    {
        // Reset selected task when search changes
        $this->form->selectedTaskId = null;
    }

    public function updatedFormRewardSearch(): void
    {
        // Reset selected reward when search changes
        $this->form->selectedRewardId = null;
    }

    public function updatedFormPunishmentSearch(): void
    {
        // Reset selected punishment when search changes
        $this->form->selectedPunishmentId = null;
    }

    public function updatedFormSelectedTaskId(): void
    {
        // Update the search field to show the selected task title
        if ($this->form->selectedTaskId) {
            $tasks = $this->getAllAvailableTasks();
            $this->form->taskSearch = $tasks[$this->form->selectedTaskId] ?? '';
        }
    }

    public function updatedFormSelectedRewardId(): void
    {
        // Update the search field to show the selected reward title
        if ($this->form->selectedRewardId) {
            $rewards = $this->getAllAvailableRewards();
            $this->form->rewardSearch = $rewards[$this->form->selectedRewardId]['title'] ?? '';
        }
    }

    public function updatedFormSelectedPunishmentId(): void
    {
        // Update the search field to show the selected punishment title
        if ($this->form->selectedPunishmentId) {
            $punishments = $this->getAllAvailablePunishments();
            $this->form->punishmentSearch = $punishments[$this->form->selectedPunishmentId]['title'] ?? '';
        }
    }

    public function selectTask(int $id, string $title): void
    {
        $this->form->selectedTaskId = $id;
        $this->form->taskSearch = $title;
    }

    public function getSelectedTaskTitle(): ?string
    {
        if (!$this->form->selectedTaskId) {
            return null;
        }
        
        $tasks = $this->getAllAvailableTasks();
        return $tasks[$this->form->selectedTaskId] ?? null;
    }

    public function getSelectedRewardTitle(): ?string
    {
        if (!$this->form->selectedRewardId) {
            return null;
        }
        
        $rewards = $this->getAllAvailableRewards();
        return $rewards[$this->form->selectedRewardId]['title'] ?? null;
    }

    public function getSelectedPunishmentTitle(): ?string
    {
        if (!$this->form->selectedPunishmentId) {
            return null;
        }
        
        $punishments = $this->getAllAvailablePunishments();
        return $punishments[$this->form->selectedPunishmentId]['title'] ?? null;
    }

    protected $listeners = ['selectTask', 'selectReward', 'selectPunishment'];

    public function selectReward(int $id, string $title): void
    {
        $this->form->selectedRewardId = $id;
        $this->form->rewardSearch = $title;
    }

    public function selectPunishment(int $id, string $title): void
    {
        $this->form->selectedPunishmentId = $id;
        $this->form->punishmentSearch = $title;
    }

    public function updatedFormRewardSelection(): void
    {
        if ($this->form->rewardSelection === 'existing') {
            $this->form->rewardTitle = '';
            $this->form->rewardDescription = '';
            $this->form->rewardDifficultyLevel = 3;
        }
    }

    public function updatedFormPunishmentSelection(): void
    {
        if ($this->form->punishmentSelection === 'existing') {
            $this->form->punishmentTitle = '';
            $this->form->punishmentDescription = '';
            $this->form->punishmentDifficultyLevel = 3;
        }
    }

    public function updatedFormDifficultyLevel(): void
    {
        // Sync reward and punishment difficulty levels with task difficulty when creating new ones
        if ($this->form->rewardSelection === 'create') {
            $this->form->rewardDifficultyLevel = $this->form->difficultyLevel;
        }
        if ($this->form->punishmentSelection === 'create') {
            $this->form->punishmentDifficultyLevel = $this->form->difficultyLevel;
        }
    }

    public function submit(): mixed
    {
        $this->isSubmitting = true;
        $this->successMessage = null;

        // Validate the form - this will automatically show inline errors
        $this->form->validate();

        $user = Auth::user();
        if (!$user) {
            $this->addError('form', 'You must be logged in to create a custom task.');
            $this->isSubmitting = false;
            return null;
        }

        try {
            // Get or create the task
            $task = $this->getOrCreateTask($user);
            
            // Get or create the reward
            $reward = $this->getOrCreateReward($user);
            
            // Get or create the punishment
            $punishment = $this->getOrCreatePunishment($user);

            // Create the custom task assignment
            $result = CreateCustomTaskAction::run(
                user: $user,
                task: $task,
                reward: $reward,
                punishment: $punishment,
                keepPrivate: $this->form->keepPrivate,
            );

            $this->successMessage = 'Your custom task has been created and assigned to you! ' . 
                ($this->form->keepPrivate ? 'This task is private to you.' : 'This task has been submitted for community review.');
            
            return redirect()->route('app.tasks');
        } catch (\Exception $e) {
            $this->addError('form', $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function getOrCreateTask($user): Task
    {
        if ($this->form->taskSelection === 'existing') {
            return Task::findOrFail($this->form->selectedTaskId);
        }

        // Create new task
        return Task::create([
            'title' => $this->form->title,
            'description' => $this->form->description,
            'difficulty_level' => $this->form->difficultyLevel,
            'duration_time' => $this->form->durationTime,
            'duration_type' => $this->form->durationType,
            'target_user_type' => $this->form->targetUserType,
            'user_id' => $user->id,
            'status' => $this->form->keepPrivate ? ContentStatus::Approved : ContentStatus::Pending,
            'is_premium' => $this->form->isPremium,
        ]);
    }

    private function getOrCreateReward($user): ?Outcome
    {
        if ($this->form->rewardSelection === 'existing') {
            return $this->form->selectedRewardId ? Outcome::findOrFail($this->form->selectedRewardId) : null;
        }

        // Create new reward
        return Outcome::create([
            'title' => $this->form->rewardTitle,
            'description' => $this->form->rewardDescription,
            'difficulty_level' => $this->form->rewardDifficultyLevel,
            'target_user_type' => $this->form->targetUserType,
            'user_id' => $user->id,
            'status' => $this->form->keepPrivate ? ContentStatus::Approved : ContentStatus::Pending,
            'intended_type' => 'reward',
            'is_premium' => $this->form->isPremium,
        ]);
    }

    private function getOrCreatePunishment($user): ?Outcome
    {
        if ($this->form->punishmentSelection === 'existing') {
            return $this->form->selectedPunishmentId ? Outcome::findOrFail($this->form->selectedPunishmentId) : null;
        }

        // Create new punishment
        return Outcome::create([
            'title' => $this->form->punishmentTitle,
            'description' => $this->form->punishmentDescription,
            'difficulty_level' => $this->form->punishmentDifficultyLevel,
            'target_user_type' => $this->form->targetUserType,
            'user_id' => $user->id,
            'status' => $this->form->keepPrivate ? ContentStatus::Approved : ContentStatus::Pending,
            'intended_type' => 'punishment',
            'is_premium' => $this->form->isPremium,
        ]);
    }

    public function resetForm(): void
    {
        $this->form->reset();
    }

    public function getAvailableTasks(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $query = Task::approved()
            ->where(function ($query) use ($user) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $user->user_type);
            });

        // Add search filter if search term is provided
        if (!empty($this->form->taskSearch)) {
            $query->where('title', 'like', '%' . $this->form->taskSearch . '%');
        }

        return $query->orderBy('title')
            ->get()
            ->mapWithKeys(function (Task $task) {
                return [$task->id => $task->title];
            })
            ->toArray();
    }

    public function getAllAvailableTasks(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        return Task::approved()
            ->where(function ($query) use ($user) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $user->user_type);
            })
            ->orderBy('title')
            ->get()
            ->mapWithKeys(function (Task $task) {
                return [$task->id => $task->title];
            })
            ->toArray();
    }


    public function getAllAvailableRewards(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        return Outcome::approved()
            ->where('intended_type', 'reward')
            ->where(function ($query) use ($user) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $user->user_type);
            })
            ->orderBy('title')
            ->get()
            ->mapWithKeys(function (Outcome $outcome) {
                return [$outcome->id => [
                    'title' => $outcome->title,
                    'description' => $outcome->description
                ]];
            })
            ->toArray();
    }


    public function getAllAvailablePunishments(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        return Outcome::approved()
            ->where('intended_type', 'punishment')
            ->where(function ($query) use ($user) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $user->user_type);
            })
            ->orderBy('title')
            ->get()
            ->mapWithKeys(function (Outcome $outcome) {
                return [$outcome->id => [
                    'title' => $outcome->title,
                    'description' => $outcome->description
                ]];
            })
            ->toArray();
    }

    public function getAvailableRewards(): array
    {
        $rewards = $this->getAllAvailableRewards();
        
        if (empty($this->form->rewardSearch)) {
            return $rewards;
        }
        
        $searchTerm = strtolower($this->form->rewardSearch);
        
        return array_filter($rewards, function ($reward) use ($searchTerm) {
            return str_contains(strtolower($reward['title']), $searchTerm) ||
                   str_contains(strtolower($reward['description']), $searchTerm);
        });
    }

    public function getAvailablePunishments(): array
    {
        $punishments = $this->getAllAvailablePunishments();
        
        if (empty($this->form->punishmentSearch)) {
            return $punishments;
        }
        
        $searchTerm = strtolower($this->form->punishmentSearch);
        
        return array_filter($punishments, function ($punishment) use ($searchTerm) {
            return str_contains(strtolower($punishment['title']), $searchTerm) ||
                   str_contains(strtolower($punishment['description']), $searchTerm);
        });
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
