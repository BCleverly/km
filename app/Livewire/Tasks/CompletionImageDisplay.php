<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Tasks\UserAssignedTask;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CompletionImageDisplay extends Component
{
    public UserAssignedTask $assignedTask;
    public bool $showFullSize = false;

    public function mount(UserAssignedTask $assignedTask): void
    {
        $this->assignedTask = $assignedTask;
    }

    public function toggleFullSize(): void
    {
        $this->showFullSize = !$this->showFullSize;
    }

    public function render(): View
    {
        return view('livewire.tasks.completion-image-display');
    }
}
