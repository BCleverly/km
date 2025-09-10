<?php

declare(strict_types=1);

namespace App\Livewire\Stories;

use App\Models\Story;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class CreateStory extends Component
{
    #[Rule('required|string|max:255', message: 'Story title is required and must be 255 characters or less.')]
    public string $title = '';

    #[Rule('required|string|max:500', message: 'Story summary is required and must be 500 characters or less.')]
    public string $summary = '';

    #[Rule('required|string|min:100', message: 'Story content must be at least 100 words.')]
    public string $content = '';

    #[Rule('boolean')]
    public bool $is_premium = false;

    public function save(): void
    {
        $this->validate();

        if (!Auth::check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to create a story',
                'type' => 'error',
            ]);
            return;
        }

        // Calculate word count
        $wordCount = str_word_count(strip_tags($this->content));
        
        if ($wordCount < 100) {
            $this->addError('content', 'Story must be at least 100 words. Current word count: ' . $wordCount);
            return;
        }

        $story = Story::create([
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'word_count' => $wordCount,
            'reading_time_minutes' => max(1, ceil($wordCount / 200)), // 200 words per minute
            'user_id' => Auth::id(),
            'is_premium' => $this->is_premium,
            'status' => 1, // Pending approval
        ]);

        $this->dispatch('show-notification', [
            'message' => 'Story submitted successfully! It will be reviewed before being published.',
            'type' => 'success',
        ]);

        // Reset form
        $this->title = '';
        $this->summary = '';
        $this->content = '';
        $this->is_premium = false;

        // Redirect to stories list
        $this->redirectRoute('stories.index');
    }

    public function getWordCount(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    public function getReadingTime(): int
    {
        $wordCount = $this->getWordCount();
        return max(1, ceil($wordCount / 200)); // 200 words per minute
    }

    public function render(): View
    {
        return view('livewire.stories.create-story');
    }
}