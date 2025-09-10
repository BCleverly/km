<?php

declare(strict_types=1);

namespace App\Livewire\Stories;

use App\Models\Story;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowStory extends Component
{
    public Story $story;

    public function mount(Story $story): void
    {
        $this->story = $story;
        
        // Increment view count
        $this->story->incrementViewCount();
    }

    #[Computed]
    public function connectedStories()
    {
        return $this->story->connectedStories()
            ->approved()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function connectingStories()
    {
        return $this->story->connectingStories()
            ->approved()
            ->limit(5)
            ->get();
    }

    public function reportStory(): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to report content',
                'type' => 'error',
            ]);
            return;
        }

        // Increment report count
        $this->story->incrementReportCount();

        $this->dispatch('show-notification', [
            'message' => 'Story reported successfully. Our moderation team will review it.',
            'type' => 'success',
        ]);
    }

    public function render(): View
    {
        return view('livewire.stories.show-story');
    }
}