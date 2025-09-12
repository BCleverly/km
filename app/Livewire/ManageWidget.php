<?php

namespace App\Livewire;

use App\Actions\Tasks\AssignRandomTask;
use App\Actions\Tasks\CompleteTask;
use App\Actions\Tasks\FailTask;
use App\Models\Tasks\UserAssignedTask;
use App\TaskStatus;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageWidget extends Component
{
    use WithFileUploads;

    #[Locked]
    public bool $canUploadImage = false;

    public $completionImage;

    public function mount()
    {
        $this->canUploadImage = auth()->user()?->canUploadCompletionImages() ?? false;
    }

    public function render()
    {
        return view('livewire.manage-widget');
    }

    #[Computed]
    public function activeTask(): ?UserAssignedTask
    {
        return auth()->user()->assignedTasks()
            ->where('status', TaskStatus::Assigned)
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->first();
    }

    /**
     * Assign a random task to the user
     */
    public function assignRandomTask()
    {
        $user = auth()->user();
        $result = AssignRandomTask::run($user);

        $this->dispatch('notify', [
            'type' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * Fail the current active task
     */
    public function failTask()
    {
        $user = auth()->user();
        $result = FailTask::run($user);

        $this->dispatch('notify', [
            'type' => $result['success'] ? 'warning' : 'error',
            'message' => $result['message'],
        ]);
    }



    /**
     * Fail the current active task
     */
    public function completeTask()
    {
        $user = auth()->user();
        if (!$user) {
            $this->addError('general', 'You must be logged in to complete a task.');
            return;
        }

        // Call the CompleteTask action
        $result = CompleteTask::run(
            user: $user,
            completionImage: $this->completionImage,
        );

        $this->reset('completionImage');

        if ($result['success']) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $result['message']
            ]);

            // Dispatch event to refresh dashboard and close modal
            $this->dispatch('task-completed');

        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $result['message']
            ]);
        }
    }


}
