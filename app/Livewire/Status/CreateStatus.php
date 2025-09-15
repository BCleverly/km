<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateStatus extends Component
{
    #[Validate('required|string|max:' . Status::getMaxLength())]
    public string $content = '';

    public bool $isPublic = true;

    public function mount(): void
    {
        // Check if user has reached daily limit
        if ($this->hasReachedDailyLimit()) {
            $this->dispatch('show-notification', [
                'message' => 'You have reached your daily status limit.',
                'type' => 'error',
            ]);
        }
    }

    public function create(): void
    {
        $this->validate();

        if (!Auth::check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to create a status.',
                'type' => 'error',
            ]);
            return;
        }

        if ($this->hasReachedDailyLimit()) {
            $this->dispatch('show-notification', [
                'message' => 'You have reached your daily status limit.',
                'type' => 'error',
            ]);
            return;
        }

        $status = Status::create([
            'content' => $this->content,
            'user_id' => Auth::id(),
            'is_public' => $this->isPublic,
        ]);

        $this->reset(['content']);
        $this->isPublic = true;

        $this->dispatch('show-notification', [
            'message' => 'Status created successfully!',
            'type' => 'success',
        ]);

        $this->dispatch('status-created', [
            'statusId' => $status->id,
        ]);
    }

    public function getCharacterCountProperty(): int
    {
        return strlen($this->content);
    }

    public function getRemainingCharactersProperty(): int
    {
        return max(0, Status::getMaxLength() - $this->characterCount);
    }

    public function getMaxLengthProperty(): int
    {
        return Status::getMaxLength();
    }

    private function hasReachedDailyLimit(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasReachedDailyStatusLimit();
    }

    public function render()
    {
        return view('livewire.status.create-status');
    }
}