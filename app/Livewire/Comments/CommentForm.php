<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentForm extends Component
{
    #[Locked]
    public Model $commentable;
    
    public ?int $parentId = null;
    public string $content = '';
    public bool $showPreview = false;
    public string $previewContent = '';

    public function mount(Model $commentable, ?int $parentId = null): void
    {
        $this->commentable = $commentable;
        $this->parentId = $parentId;
    }

    #[Computed]
    public function isReply(): bool
    {
        return !is_null($this->parentId);
    }

    #[Computed]
    public function placeholder(): string
    {
        return $this->isReply ? 'Leave a reply...' : 'Leave a comment...';
    }

    #[Computed]
    public function submitButtonText(): string
    {
        return $this->isReply ? 'Reply' : 'Comment';
    }

    public function render(): View
    {
        return view('livewire.comments.comment-form');
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:5000|min:1',
        ];
    }

    public function submit(): void
    {
        if (!Auth::check()) {
            $this->addError('content', 'You must be logged in to comment.');
            return;
        }

        $this->validate();

        $comment = $this->commentable->addComment(
            content: $this->content,
            parentId: $this->parentId,
            userId: Auth::id()
        );

        $this->content = '';
        $this->showPreview = false;
        $this->previewContent = '';

        if ($this->parentId) {
            $this->dispatch('reply-added');
        } else {
            $this->dispatch('comment-added');
        }
    }

    public function togglePreview(): void
    {
        $this->showPreview = !$this->showPreview;
        
        if ($this->showPreview) {
            $this->previewContent = $this->content;
        }
    }

    public function insertMarkdown(string $markdown): void
    {
        $this->content .= $markdown;
    }

    public function insertBold(): void
    {
        $this->insertMarkdown('**bold text**');
    }

    public function insertItalic(): void
    {
        $this->insertMarkdown('*italic text*');
    }

    public function insertQuote(): void
    {
        $this->insertMarkdown('> quote text');
    }

    public function insertUnorderedList(): void
    {
        $this->insertMarkdown('- list item');
    }

    public function insertOrderedList(): void
    {
        $this->insertMarkdown('1. list item');
    }

    public function insertLink(): void
    {
        $this->insertMarkdown('[link text](https://example.com)');
    }

    public function insertCodeBlock(): void
    {
        $this->insertMarkdown('```php' . "\n" . '// code here' . "\n" . '```');
    }

    public function insertInlineCode(): void
    {
        $this->insertMarkdown('`inline code`');
    }

    #[On('start-reply')]
    public function startReply(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    #[On('cancel-reply')]
    public function cancelReply(): void
    {
        $this->parentId = null;
        $this->content = '';
        $this->showPreview = false;
        $this->previewContent = '';
    }
}