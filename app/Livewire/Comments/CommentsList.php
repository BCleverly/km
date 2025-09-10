<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CommentsList extends Component
{
    use WithPagination;

    #[Locked]
    public Model $commentable;
    
    #[Locked]
    public int $perPage = 10;
    
    #[Locked]
    public bool $showForm = true;
    
    public ?int $replyingTo = null;

    public function mount(Model $commentable, int $perPage = 10, bool $showForm = true): void
    {
        $this->commentable = $commentable;
        $this->perPage = $perPage;
        $this->showForm = $showForm;
    }

    #[Computed]
    public function comments()
    {
        return $this->commentable
            ->topLevelComments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render(): View
    {
        return view('livewire.comments.comments-list');
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
        // No need to reset page for deletes
    }

    public function startReply(int $commentId): void
    {
        $this->replyingTo = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyingTo = null;
    }

    #[On('reply-added')]
    public function handleReplyAdded(): void
    {
        $this->replyingTo = null;
    }
}