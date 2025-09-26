<?php

declare(strict_types=1);

namespace App\Livewire\User\SocialProfile;

use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PostsTab extends Component
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

    public function render()
    {
        return view('livewire.user.social-profile.posts-tab');
    }
}
