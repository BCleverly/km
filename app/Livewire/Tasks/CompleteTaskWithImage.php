<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\CompleteTask;
use App\Models\Tasks\UserAssignedTask;
use App\TaskStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompleteTaskWithImage extends Component
{
    use WithFileUploads;

    public UserAssignedTask $assignedTask;
    
    #[Validate('nullable|image|max:10240')] // 10MB max
    public $completionImage = null;
    
    #[Validate('nullable|string|max:1000')]
    public string $completionNote = '';

    public bool $showImageUpload = false;
    public bool $isSubmitting = false;

    public function mount(UserAssignedTask $assignedTask): void
    {
        $this->assignedTask = $assignedTask;
        $this->showImageUpload = auth()->user()->canUploadCompletionImages();
    }

    public function updatedCompletionImage(): void
    {
        $this->validateOnly('completionImage');
    }

    public function completeTask(): void
    {
        $this->isSubmitting = true;
        
        try {
            // Validate the form
            $this->validate();
            
            $user = Auth::user();
            if (!$user) {
                $this->addError('general', 'You must be logged in to complete a task.');
                $this->isSubmitting = false;
                return;
            }

            // Call the CompleteTask action
            $result = CompleteTask::run(
                user: $user,
                completionImage: $this->completionImage,
                completionNote: $this->completionNote ?: null
            );

            if ($result['success']) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => $result['message']
                ]);
                
                // Reset form
                $this->completionImage = null;
                $this->completionNote = '';
                
                // Redirect to dashboard or refresh
                $this->dispatch('task-completed');
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => $result['message']
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire can handle them
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Task completion error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'task_id' => $this->assignedTask->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred while completing the task. Please try again.'
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render(): View
    {
        return view('livewire.tasks.complete-task-with-image');
    }
}
