<?php

namespace App\Livewire\CoupleTasks;

use App\Models\CoupleTask;
use Livewire\Component;

class MyTasks extends Component
{
    public ?CoupleTask $selectedTask = null;

    public string $completionNotes = '';

    public string $thankYouMessage = '';

    public function mount()
    {
        // Check authorization
        if (! auth()->user()->canReceiveCoupleTasks()) {
            abort(403, 'You do not have permission to receive couple tasks.');
        }
    }

    public function selectTask(CoupleTask $task)
    {
        // Ensure the user can only see their own tasks
        if ($task->assigned_to !== auth()->id()) {
            abort(403, 'You can only view your own tasks.');
        }

        $this->selectedTask = $task;
        $this->completionNotes = '';
        $this->thankYouMessage = '';
    }

    public function completeTask()
    {
        if (! $this->selectedTask || ! $this->selectedTask->canBeCompleted()) {
            return;
        }

        $this->validate([
            'completionNotes' => 'nullable|string|max:1000',
        ]);

        $this->selectedTask->markAsCompleted($this->completionNotes);

        session()->flash('message', 'Task completed successfully!');
        $this->dispatch('task-completed');
        $this->selectedTask = null;
    }

    public function failTask()
    {
        if (! $this->selectedTask || ! $this->selectedTask->canBeCompleted()) {
            return;
        }

        $this->selectedTask->markAsFailed();

        session()->flash('message', 'Task marked as failed.');
        $this->dispatch('task-failed');
        $this->selectedTask = null;
    }

    public function declineTask()
    {
        if (! $this->selectedTask || ! $this->selectedTask->canBeCompleted()) {
            return;
        }

        $this->selectedTask->markAsDeclined();

        session()->flash('message', 'Task declined.');
        $this->dispatch('task-declined');
        $this->selectedTask = null;
    }

    public function sendThankYou()
    {
        if (! $this->selectedTask || ! $this->selectedTask->canBeThanked()) {
            return;
        }

        $this->validate([
            'thankYouMessage' => 'required|string|max:1000',
        ]);

        $this->selectedTask->addThankYou($this->thankYouMessage);

        session()->flash('message', 'Thank you message sent to your partner!');
        $this->dispatch('thank-you-sent');
        $this->selectedTask = null;
    }

    public function getActiveTasks()
    {
        return CoupleTask::where('assigned_to', auth()->id())
            ->active()
            ->with(['assignedBy.profile', 'reward', 'punishment'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function getCompletedTasks()
    {
        return CoupleTask::where('assigned_to', auth()->id())
            ->completed()
            ->with(['assignedBy.profile', 'reward', 'punishment'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getOverdueTasks()
    {
        return CoupleTask::where('assigned_to', auth()->id())
            ->overdue()
            ->with(['assignedBy.profile', 'reward', 'punishment'])
            ->orderBy('deadline_at', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.couple-tasks.my-tasks', [
            'activeTasks' => $this->getActiveTasks(),
            'completedTasks' => $this->getCompletedTasks(),
            'overdueTasks' => $this->getOverdueTasks(),
        ])->layout('components.layouts.app', [
            'title' => 'My Partner Tasks - Kink Master',
        ]);
    }
}
