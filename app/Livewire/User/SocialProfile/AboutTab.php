<?php

declare(strict_types=1);

namespace App\Livewire\User\SocialProfile;

use App\Models\Profile;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AboutTab extends Component
{
    #[Locked]
    public ?User $user = null;

    #[Locked]
    public ?Profile $profile = null;

    #[Locked]
    public bool $isOwnProfile = false;

    public function mount(?User $user = null, ?Profile $profile = null, bool $isOwnProfile = false): void
    {
        $this->user = $user;
        $this->profile = $profile;
        $this->isOwnProfile = $isOwnProfile;
    }

    #[Computed]
    public function displayName(): string
    {
        return $this->user?->display_name ?? 'Unknown User';
    }

    #[Computed]
    public function about(): ?string
    {
        return $this->profile?->about;
    }

    #[Computed]
    public function completedTasksCount(): int
    {
        return $this->user?->tasks()->getTotalCompletedTasks() ?? 0;
    }

    #[Computed]
    public function currentStreak(): int
    {
        return $this->user?->tasks()->getCurrentStreak() ?? 0;
    }

    #[Computed]
    public function totalPoints(): int
    {
        // Points system not implemented yet, return 0 for now
        return 0;
    }

    public function render()
    {
        return view('livewire.user.social-profile.about-tab');
    }
}
