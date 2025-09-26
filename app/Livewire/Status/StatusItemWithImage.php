<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StatusItemWithImage extends Component
{
    #[Locked]
    public Status $status;


    public bool $showImageModal = false;

    public function mount(Status $status): void
    {
        $this->status = $status;
    }


    public function toggleImageModal(): void
    {
        $this->showImageModal = ! $this->showImageModal;
    }

    public function deleteStatus(): void
    {
        if (! Auth::check() || Auth::id() !== $this->status->user_id) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You are not authorized to delete this status.',
            ]);

            return;
        }

        $this->status->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status deleted successfully.',
        ]);

        $this->dispatch('status-deleted', [
            'statusId' => $this->status->id,
        ]);
    }

    public function getCanDeleteProperty(): bool
    {
        return Auth::check() && Auth::id() === $this->status->user_id;
    }

    public function getTimeAgoProperty(): string
    {
        return $this->status->created_at->diffForHumans();
    }

    public function getFormattedDateProperty(): string
    {
        return $this->status->created_at->format('M j, Y \a\t g:i A');
    }

    public function getStatusImageUrlProperty(): ?string
    {
        return $this->status->status_image_url;
    }

    public function getStatusImageLargeUrlProperty(): ?string
    {
        $media = $this->status->getFirstMedia('status_images');
        if ($media) {
            try {
                return $media->getUrl('status_large');
            } catch (\Exception $e) {
                return $media->getUrl();
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.status.status-item-with-image');
    }
}
