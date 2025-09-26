<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StatusComments extends Component
{
    use WithPagination;

    #[Locked]
    public Status $status;

    #[Locked]
    public int $perPage = 5;

    public function mount(Status $status, int $perPage = 5): void
    {
        $this->status = $status;
        $this->perPage = $perPage;
    }

    #[Computed]
    public function comments()
    {
        return $this->status
            ->topLevelComments()
            ->with(['user.profile', 'replies.user.profile'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function commentsCount(): int
    {
        return $this->status->approved_comments_count;
    }

    public function render()
    {
        return view('livewire.status.status-comments');
    }

    #[On('comment-added')]
    public function refreshComments(): void
    {
        $this->resetPage();
    }

    #[On('comment-updated')]
    public function refreshCommentsAfterUpdate(): void
    {
        // No need to reset page for updates
    }

    #[On('comment-deleted')]
    public function refreshCommentsAfterDelete(): void
    {
        $this->resetPage();
    }
}