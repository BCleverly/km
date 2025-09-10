<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Profile;
use Livewire\Component;

class PublicProfile extends Component
{
    public User $user;
    public ?Profile $profile = null;

    public function mount(string $username)
    {
        // Find the user by username
        $this->profile = Profile::where('username', $username)->first();
        
        if (!$this->profile) {
            abort(404, 'User not found');
        }
        
        $this->user = $this->profile->user;
    }

    public function getCoverPhotoUrl()
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

    public function getProfilePictureUrl()
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

    public function getDisplayName()
    {
        return $this->user->display_name;
    }

    public function getUsername()
    {
        return $this->profile?->username ?? $this->user->name;
    }

    public function getAbout()
    {
        return $this->profile?->about;
    }

    public function getJoinedDate()
    {
        return $this->user->created_at->format('F Y');
    }

    public function getCompletedTasksCount()
    {
        return $this->user->stats()->getTotalCompletedTasks();
    }

    public function getCurrentStreak()
    {
        return $this->user->stats()->getCurrentStreak();
    }

    public function getTotalPoints()
    {
        // For now, we'll use completed tasks as points
        // This can be enhanced later with actual point system
        return $this->user->stats()->getTotalCompletedTasks();
    }

    public function getRecentActivities($limit = 5)
    {
        return $this->user->recentTaskActivities($limit)->get();
    }

    public function render()
    {
        return view('livewire.user.public-profile')
            ->layout('components.layouts.app', [
                'title' => $this->getDisplayName() . ' - Kink Master',
            ]);
    }
}