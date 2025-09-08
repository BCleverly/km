<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\ContentStatus;
use App\Livewire\Forms\CreateCustomTaskForm;
use App\Models\Tasks\Task;
use App\TargetUserType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitTask extends Component
{
    public CreateCustomTaskForm $taskForm;
    

    #[Computed]
    public function userTypes(): array
    {
        return collect(TargetUserType::cases())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }

    #[Computed]
    public function difficultyLevels(): array
    {
        return [
            1 => 'Very Easy',
            2 => 'Easy',
            3 => 'Medium',
            4 => 'Hard',
            5 => 'Very Hard',
            6 => 'Extreme',
        ];
    }

    public function submitTask(): void
    {
        $this->taskForm->submit();
        
        session()->flash('message', 'Your task has been submitted for review!');
        $this->taskForm->resetForm();
    }

    public function createTag(string $type, string $name): void
    {
        if (empty(trim($name))) {
            session()->flash('error', 'Tag name cannot be empty.');
            return;
        }

        $tagId = $this->taskForm->createTag($type, trim($name));

        if ($tagId) {
            // Add the new tag to the selected tags for this type
            if (!isset($this->taskForm->tags[$type])) {
                $this->taskForm->tags[$type] = [];
            }
            $this->taskForm->tags[$type][] = $tagId;

            session()->flash('message', 'New tag created and added! It will be reviewed before becoming visible to others.');
        } else {
            session()->flash('error', 'Failed to create tag. Please try again.');
        }
    }

    public function removeTag(string $type, int $tagId): void
    {
        $this->taskForm->removeTag($type, $tagId);
    }

    public function render(): View
    {
        return view('livewire.tasks.submit-task');
    }
}
