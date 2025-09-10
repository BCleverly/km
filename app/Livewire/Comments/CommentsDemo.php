<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Models\Story;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CommentsDemo extends Component
{
    public Story $story;

    public function mount(): void
    {
        // Get a sample story for demo purposes
        $this->story = Story::first() ?? Story::factory()->create();
    }

    public function render(): View
    {
        return view('livewire.comments.comments-demo');
    }
}