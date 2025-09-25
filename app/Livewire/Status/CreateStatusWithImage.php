<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateStatusWithImage extends Component
{
    use WithFileUploads;

    #[Validate('nullable|string|max:280')]
    public string $content = '';

    public bool $isPublic = true;

    public $image = null;

    public bool $showImagePreview = false;

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

    public function updatedImage(): void
    {
        try {
            $this->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);

            if ($this->image) {
                $this->showImagePreview = true;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Reset image on validation failure
            $this->image = null;
            $this->showImagePreview = false;
            throw $e;
        }
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->showImagePreview = false;
    }

    public function resetImage(): void
    {
        $this->image = null;
        $this->showImagePreview = false;
        $this->clearErrors('image');
    }

    public function create(): void
    {
        $this->validate([
            'content' => 'nullable|string|max:280',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Ensure at least content or image is provided
        if (empty(trim($this->content)) && ! $this->image) {
            $this->addError('content', 'Please provide either text content or an image.');

            return;
        }

        if (! Auth::check()) {
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

        // Handle image upload if present
        if ($this->image && $this->image->isValid()) {
            try {
                $status->addMedia($this->image->getRealPath())
                    ->usingName($this->image->getClientOriginalName())
                    ->usingFileName($this->image->getClientOriginalName())
                    ->toMediaCollection('status_images');
            } catch (\Exception $e) {
                // If image upload fails, delete the status and show error
                $status->delete();
                $this->addError('image', 'Failed to upload image. Please try again.');
                return;
            }
        }

        $this->reset(['content', 'image', 'showImagePreview']);
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

    public function getImagePreviewUrlProperty(): ?string
    {
        if ($this->image && $this->image->isValid()) {
            return $this->image->temporaryUrl();
        }

        return null;
    }

    private function hasReachedDailyLimit(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasReachedDailyStatusLimit();
    }

    public function render()
    {
        return view('livewire.status.create-status-with-image');
    }
}
