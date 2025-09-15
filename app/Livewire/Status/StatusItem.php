<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StatusItem extends Component
{
    #[Locked]
    public Status $status;

    public bool $showComments = false;

    public function mount(Status $status): void
    {
        $this->status = $status;
    }

    public function toggleComments(): void
    {
        $this->showComments = !$this->showComments;
    }

    public function deleteStatus(): void
    {
        if (!Auth::check() || Auth::id() !== $this->status->user_id) {
            $this->dispatch('show-notification', [
                'message' => 'You are not authorized to delete this status.',
                'type' => 'error',
            ]);
            return;
        }

        $this->status->delete();

        $this->dispatch('show-notification', [
            'message' => 'Status deleted successfully.',
            'type' => 'success',
        ]);

        $this->dispatch('status-deleted', [
            'statusId' => $this->status->id,
        ]);
    }

    public function getCanDeleteProperty(): bool
    {
        return Auth::check() && Auth::id() === $this->status->user_id;
    }

    public function getTimeAgoProperty(): string
    {
        return $this->status->created_at->diffForHumans();
    }

    public function getFormattedDateProperty(): string
    {
        return $this->status->created_at->format('M j, Y \a\t g:i A');
    }

    public function render()
    {
        return view('livewire.status.status-item');
    }
}