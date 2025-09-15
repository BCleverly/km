<?php

declare(strict_types=1);

namespace App\Livewire\Stories;

use App\Models\Story;
use App\Services\ViewTrackingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowStory extends Component
{
    public Story $story;

    public function mount(Story $story, ViewTrackingService $viewTrackingService): void
    {
        $this->story = $story;
        
        // Track view with abuse prevention
        $userId = auth()->id();
        $viewTrackingService->trackView('story', $story->id, $userId);
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
            $this->dispatch('notify', [
                'message' => 'Please log in to report content',
                'type' => 'error',
            ]);
            return;
        }

        // Increment report count
        $this->story->incrementReportCount();

        $this->dispatch('notify', [
            'message' => 'Story reported successfully. Our moderation team will review it.',
            'type' => 'success',
        ]);
    }

    public function render(): View
    {
        return view('livewire.stories.show-story')
            ->layout('components.layouts.app', [
                'title' => $this->story->title . ' - Kink Master'
            ]);
    }
}