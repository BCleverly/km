<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class StatusListWithImages extends Component
{
    public ?User $user = null;

    public int $limit = 10;

    public bool $showCreateForm = false;

    public function mount(?User $user = null, int $limit = 10): void
    {
        $this->user = $user;
        $this->limit = $limit;
    }

    #[Computed]
    public function statuses(): Collection
    {
        $query = Status::with(['user', 'user.profile'])
            ->withCount('approvedComments')
            ->public()
            ->recent($this->limit);

        if ($this->user) {
            $query->forUser($this->user);
        }

        return $query->get();
    }

    #[Computed]
    public function canCreateStatus(): bool
    {
        return auth()->check() && ! $this->hasReachedDailyLimit();
    }

    #[Computed]
    public function dailyStatusCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return auth()->user()->getTodayStatusCount();
    }

    #[Computed]
    public function maxStatusesPerDay(): int
    {
        return config('app.statuses.max_per_user_per_day', 10);
    }

    public function toggleCreateForm(): void
    {
        if (! $this->canCreateStatus) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You have reached your daily status limit.',
            ]);

            return;
        }

        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function refreshStatuses(): void
    {
        $this->dispatch('$refresh');
    }

    #[On('status-created')]
    public function handleStatusCreated(): void
    {
        // Refresh the status list when a new status is created
        $this->dispatch('$refresh');
    }

    #[On('status-deleted')]
    public function handleStatusDeleted(): void
    {
        // Refresh the status list when a status is deleted
        $this->dispatch('$refresh');
    }

    private function hasReachedDailyLimit(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasReachedDailyStatusLimit();
    }

    public function render()
    {
        return view('livewire.status.status-list-with-images');
    }
}
