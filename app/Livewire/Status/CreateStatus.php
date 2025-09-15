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
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You have reached your daily status limit.',
            ]);
        }
    }

    public function create(): void
    {
        $this->validate();

        if (!Auth::check()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please log in to create a status.',
            ]);
            return;
        }

        if ($this->hasReachedDailyLimit()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You have reached your daily status limit.',
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

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status created successfully!',
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