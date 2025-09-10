<?php

declare(strict_types=1);

namespace App\Livewire\Fantasies;

use App\Models\Fantasy;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class CreateFantasy extends Component
{
    #[Rule('required|string|max:280', message: 'Fantasy content is required and must be 280 words or less.')]
    public string $content = '';

    #[Rule('boolean')]
    public bool $is_premium = false;

    public function save(): void
    {
        $this->validate();

        if (!Auth::check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to create a fantasy',
                'type' => 'error',
            ]);
            return;
        }

        // Calculate word count
        $wordCount = str_word_count(strip_tags($this->content));
        
        if ($wordCount > 280) {
            $this->addError('content', 'Fantasy must be 280 words or less. Current word count: ' . $wordCount);
            return;
        }

        $fantasy = Fantasy::create([
            'content' => $this->content,
            'word_count' => $wordCount,
            'user_id' => Auth::id(),
            'is_premium' => $this->is_premium,
            'status' => 1, // Pending approval
        ]);

        $this->dispatch('show-notification', [
            'message' => 'Fantasy submitted successfully! It will be reviewed before being published.',
            'type' => 'success',
        ]);

        // Reset form
        $this->content = '';
        $this->is_premium = false;

        // Redirect to fantasies list
        $this->redirectRoute('fantasies.index');
    }

    public function getWordCount(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    public function getRemainingWords(): int
    {
        return max(0, 280 - $this->getWordCount());
    }

    public function render(): View
    {
        return view('livewire.fantasies.create-fantasy');
    }
}