<?php

namespace App\Livewire\CoupleTasks;

use App\Enums\CoupleTaskStatus;
use App\Models\CoupleTask;
use App\Models\Tasks\Outcome;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SendTask extends Component
{
    #[Validate('required_if:task_mode,custom|string|max:255')]
    public string $title = '';

    #[Validate('required_if:task_mode,custom|string|max:2000')]
    public string $description = '';

    #[Validate('nullable|string|max:1000')]
    public string $dom_message = '';

    #[Validate('required|integer|min:1|max:10')]
    public int $difficulty_level = 1;

    #[Validate('required|integer|min:1|max:168')]
    public int $duration_hours = 24;

    #[Validate('nullable|integer|exists:outcomes,id')]
    public ?int $reward_id = null;

    #[Validate('nullable|integer|exists:outcomes,id')]
    public ?int $punishment_id = null;

    #[Validate('required|integer|exists:users,id')]
    public int $assigned_to = 0;

    public string $task_mode = 'custom'; // 'custom' or 'existing'
    
    #[Validate('nullable|integer|exists:tasks,id')]
    public ?int $selected_task_id = null;

    public function mount()
    {
        // Check authorization
        if (!auth()->user()->canAssignCoupleTasks()) {
            abort(403, 'You do not have permission to assign couple tasks.');
        }
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();

        // Verify the assigned user can receive couple tasks
        $assignedUser = User::findOrFail($this->assigned_to);
        if (!$assignedUser->canReceiveCoupleTasks()) {
            $this->addError('assigned_to', 'This user cannot receive couple tasks.');
            return;
        }

        // For couple accounts, ensure they're assigning to their partner
        if ($user->user_type === \App\TargetUserType::Couple) {
            $partner = $user->getCouplePartner();
            if (!$partner || $partner->id !== $this->assigned_to) {
                $this->addError('assigned_to', 'You can only assign tasks to your partner.');
                return;
            }
        }

        // Create the couple task
        $coupleTask = CoupleTask::create([
            'assigned_by' => $user->id,
            'assigned_to' => $this->assigned_to,
            'title' => $this->title,
            'description' => $this->description,
            'dom_message' => $this->dom_message,
            'difficulty_level' => $this->difficulty_level,
            'duration_hours' => $this->duration_hours,
            'status' => CoupleTaskStatus::Pending,
            'reward_id' => $this->reward_id,
            'punishment_id' => $this->punishment_id,
            'assigned_at' => now(),
            'deadline_at' => now()->addHours($this->duration_hours),
        ]);

        // Notify the sub that they have a new task
        $assignedUser->notify(new \App\Notifications\CoupleTaskAssigned($coupleTask));

        // Reset form
        $this->reset(['title', 'description', 'dom_message', 'difficulty_level', 'duration_hours', 'reward_id', 'punishment_id', 'assigned_to']);

        session()->flash('message', 'Task sent to your partner successfully!');
        $this->dispatch('task-sent');
    }

    public function getAvailablePartners()
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            // Admins can assign to any user who can receive couple tasks
            return User::whereHas('profile', function ($query) {
                $query->whereNotNull('bdsm_role');
            })->where('id', '!=', $user->id)->get();
        }

        if ($user->hasLifetimeSubscription()) {
            // Lifetime users can assign to any user who can receive couple tasks
            return User::whereHas('profile', function ($query) {
                $query->whereNotNull('bdsm_role');
            })->where('id', '!=', $user->id)->get();
        }

        if ($user->user_type === \App\TargetUserType::Couple) {
            // Couple users can only assign to their partner
            $user->load('partner');
            $partner = $user->getCouplePartner();
            return $partner ? collect([$partner]) : collect();
        }

        return collect();
    }

    public function getAvailableRewards()
    {
        return Outcome::where('intended_type', 'reward')
            ->where('status', \App\ContentStatus::Approved)
            ->orderBy('title')
            ->get();
    }

    public function getAvailablePunishments()
    {
        return Outcome::where('intended_type', 'punishment')
            ->where('status', \App\ContentStatus::Approved)
            ->orderBy('title')
            ->get();
    }

    public function getAvailableTasks()
    {
        return \App\Models\Tasks\Task::where('status', \App\ContentStatus::Approved)
            ->where(function($query) {
                $query->where('target_user_type', \App\TargetUserType::Any)
                      ->orWhere('target_user_type', \App\TargetUserType::Couple);
            })
            ->orderBy('title')
            ->get();
    }

    public function updatedTaskMode()
    {
        // Reset form when switching modes
        if ($this->task_mode === 'existing') {
            $this->selected_task_id = null;
        } else {
            $this->title = '';
            $this->description = '';
        }
    }

    public function updatedSelectedTaskId()
    {
        if ($this->selected_task_id) {
            $task = \App\Models\Tasks\Task::find($this->selected_task_id);
            if ($task) {
                $this->title = $task->title;
                $this->description = $task->description;
                $this->difficulty_level = $task->difficulty_level;
            }
        }
    }

    public function render()
    {
        $availableTasks = $this->getAvailableTasks();
        
        return view('livewire.couple-tasks.send-task', [
            'partners' => $this->getAvailablePartners(),
            'rewards' => $this->getAvailableRewards(),
            'punishments' => $this->getAvailablePunishments(),
            'availableTasks' => $availableTasks,
        ])->layout('components.layouts.app', [
            'title' => 'Send Task to Partner - Kink Master',
        ]);
    }
}
