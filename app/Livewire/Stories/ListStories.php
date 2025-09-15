<?php

declare(strict_types=1);

namespace App\Livewire\Stories;

use App\Models\Story;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListStories extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public bool $showPremium = false;

    #[Computed]
    public function stories(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Story::with(['user.profile', 'reactions', 'tags'])
            ->approved()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('summary', 'like', '%' . $this->search . '%')
                        ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when(!$this->showPremium, function ($query) {
                $query->where('is_premium', false);
            });

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function reportStory(int $storyId): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', [
                'message' => 'Please log in to report content',
                'type' => 'error',
            ]);
            return;
        }

        $story = Story::find($storyId);
        
        if (!$story) {
            $this->dispatch('notify', [
                'message' => 'Story not found',
                'type' => 'error',
            ]);
            return;
        }

        // Increment report count
        $story->incrementReportCount();

        $this->dispatch('notify', [
            'message' => 'Story reported successfully. Our moderation team will review it.',
            'type' => 'success',
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedShowPremium(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->showPremium = false;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.stories.list-stories')
            ->layout('components.layouts.app', [
                'title' => 'Stories - Kink Master'
            ]);
    }
}