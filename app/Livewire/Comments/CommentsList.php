<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CommentsList extends Component
{
    use WithPagination;

    #[Locked]
    public string $modelPath;

    #[Locked]
    public int $perPage = 10;

    #[Locked]
    public bool $showForm = true;

    public ?int $replyingTo = null;

    public function mount(string $modelPath, int $perPage = 10, bool $showForm = true): void
    {
        $this->modelPath = $modelPath;
        $this->perPage = $perPage;
        $this->showForm = $showForm;
    }

    #[Computed]
    public function commentable(): Model
    {
        // Parse the model path to get the model class and ID
        // Format: "App\Models\Story:123" or "App\Models\Task:456"
        [$modelClass, $modelId] = explode(':', $this->modelPath);

        return $modelClass::findOrFail($modelId);
    }

    #[Computed]
    public function comments()
    {
        $cacheKey = $this->getCommentsCacheKey();
        
        return Cache::remember($cacheKey, 3600, function () {
            return $this->commentable
                ->topLevelComments()
                ->with(['user.profile', 'replies.user.profile'])
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        });
    }

    public function render(): View
    {
        return view('livewire.comments.comments-list');
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

    #[On('start-reply')]
    public function handleStartReply(int $parentId): void
    {
        $this->replyingTo = $parentId;
    }

    #[On('cancel-reply')]
    public function handleCancelReply(): void
    {
        $this->replyingTo = null;
    }

    /**
     * Get the cache key for comments
     */
    private function getCommentsCacheKey(): string
    {
        return "comments_{$this->modelPath}_{$this->perPage}";
    }

    /**
     * Clear the comments cache for this model
     */
    public function clearCommentsCache(): void
    {
        Cache::forget($this->getCommentsCacheKey());
    }

    /**
     * Clear comments cache when a new comment is added
     */
    #[On('comment-added')]
    public function refreshComments(): void
    {
        $this->clearCommentsCache();
        $this->resetPage();
    }

    /**
     * Clear comments cache when a comment is updated
     */
    #[On('comment-updated')]
    public function refreshCommentsAfterUpdate(): void
    {
        $this->clearCommentsCache();
    }

    /**
     * Clear comments cache when a comment is deleted
     */
    #[On('comment-deleted')]
    public function refreshCommentsAfterDelete(): void
    {
        $this->clearCommentsCache();
    }

    /**
     * Clear comments cache when a reaction is added/removed
     */
    #[On('reaction-added', 'reaction-removed')]
    public function refreshCommentsAfterReaction(array $data = []): void
    {
        // Only clear cache if the reaction is for a comment in this thread
        if (isset($data['modelType']) && $data['modelType'] === 'comment') {
            $this->clearCommentsCache();
        }
    }
}
