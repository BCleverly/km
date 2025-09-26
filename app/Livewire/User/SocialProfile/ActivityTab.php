<?php

declare(strict_types=1);

namespace App\Livewire\User\SocialProfile;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ActivityTab extends Component
{
    #[Locked]
    public ?User $user = null;

    #[Locked]
    public bool $isOwnProfile = false;

    public function mount(?User $user = null, bool $isOwnProfile = false): void
    {
        $this->user = $user;
        $this->isOwnProfile = $isOwnProfile;
    }

    #[Computed]
    public function recentActivities()
    {
        if (! $this->user) {
            return collect();
        }

        return $this->user->taskActivities()
            ->with(['task', 'userAssignedTask.media'])
            ->latest('activity_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.user.social-profile.activity-tab');
    }
}
