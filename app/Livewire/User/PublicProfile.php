<?php

namespace App\Livewire\User;

use App\Models\Profile;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PublicProfile extends Component
{
    #[Locked]
    public string $username;

    public function mount(string $username)
    {
        $this->username = $username;

        // Validate that the user exists
        if (! $this->profile) {
            abort(404, 'User not found');
        }
    }

    #[Computed]
    public function profile(): ?Profile
    {
        return Profile::where('username', $this->username)->first();
    }

    #[Computed]
    public function user(): User
    {
        return $this->profile->user;
    }

    #[Computed]
    public function coverPhotoUrl()
    {
        if ($this->profile) {
            $media = $this->profile->getFirstMedia('cover_photos');
            if ($media) {
                // Try to get the converted version first
                try {
                    return $media->getUrl('cover_medium');
                } catch (\Exception $e) {
                    // If conversion isn't ready, use the original image
                    return $media->getUrl();
                }
            }
        }

        return null;
    }

    #[Computed]
    public function profilePictureUrl()
    {
        if ($this->profile) {
            $media = $this->profile->getFirstMedia('profile_pictures');
            if ($media) {
                // Try to get the converted version first
                try {
                    return $media->getUrl('profile_medium');
                } catch (\Exception $e) {
                    // If conversion isn't ready, use the original image
                    return $media->getUrl();
                }
            }
        }

        // Fallback to Gravatar
        return $this->user->gravatar_url;
    }

    #[Computed]
    public function completedTasksCount()
    {
        return $this->user->stats()->getTotalCompletedTasks();
    }

    #[Computed]
    public function currentStreak()
    {
        return $this->user->stats()->getCurrentStreak();
    }

    #[Computed]
    public function totalPoints()
    {
        // For now, we'll use completed tasks as points
        // This can be enhanced later with actual point system
        return $this->user->stats()->getTotalCompletedTasks();
    }

    #[Computed]
    public function recentActivities($limit = 5)
    {
        return $this->user->recentTaskActivities($limit)->get();
    }

    #[Computed]
    public function displayName()
    {
        return $this->user->display_name ?? $this->user->name;
    }

    #[Computed]
    public function joinedDate()
    {
        return $this->user->created_at->format('F Y');
    }

    #[Computed]
    public function about()
    {
        return $this->profile?->about;
    }

    #[Computed]
    public function recentStatuses($limit = 5)
    {
        return $this->user->statuses()
            ->public()
            ->recent($limit)
            ->get();
    }

    public function render()
    {
        return view('livewire.user.public-profile')
            ->layout('components.layouts.app', [
                'title' => $this->user->display_name.' - Kink Master',
            ]);
    }
}
