<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentItem extends Component
{
    #[Locked]
    public Comment $comment;
    
    public bool $showReplies = true;
    public bool $isEditing = false;
    public string $editContent = '';

    public function mount(Comment $comment, bool $showReplies = true): void
    {
        $this->comment = $comment;
        $this->showReplies = $showReplies;
        $this->editContent = $this->comment->content;
    }

    #[Computed]
    public function replies()
    {
        return $this->showReplies 
            ? $this->comment->replies()->with('user')->orderBy('created_at', 'asc')->get()
            : collect();
    }

    #[Computed]
    public function canEdit(): bool
    {
        return Auth::check() && (
            Auth::user()->id === $this->comment->user_id ||
            Auth::user()->hasPermissionTo('edit comments')
        );
    }

    #[Computed]
    public function canDelete(): bool
    {
        return Auth::check() && (
            Auth::user()->id === $this->comment->user_id ||
            Auth::user()->hasPermissionTo('delete comments')
        );
    }

    #[Computed]
    public function canReply(): bool
    {
        return Auth::check() && $this->comment->depth < 3; // Limit nesting depth
    }

    public function render(): View
    {
        return view('livewire.comments.comment-item');
    }

    public function startEdit(): void
    {
        if (!$this->canEdit()) {
            return;
        }

        $this->isEditing = true;
        $this->editContent = $this->comment->content;
    }

    public function cancelEdit(): void
    {
        $this->isEditing = false;
        $this->editContent = $this->comment->content;
    }

    public function saveEdit(): void
    {
        if (!$this->canEdit()) {
            return;
        }

        $this->validate([
            'editContent' => 'required|string|max:5000',
        ]);

        $this->comment->update([
            'content' => $this->editContent,
        ]);

        $this->isEditing = false;
        $this->dispatch('comment-updated');
    }

    public function deleteComment(): void
    {
        if (!$this->canDelete()) {
            return;
        }

        $this->comment->delete();
        $this->dispatch('comment-deleted');
    }

    public function toggleReplies(): void
    {
        $this->showReplies = !$this->showReplies;
    }


    #[On('reply-added')]
    public function refreshReplies(): void
    {
        $this->comment->refresh();
    }
}