<?php

declare(strict_types=1);

namespace App\Livewire\User\SocialProfile;

use App\Models\Profile;
use App\Models\Status;
use App\Models\Tasks\UserAssignedTask;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MediaTab extends Component
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
    public function allMedia()
    {
        if (! $this->user) {
            return collect();
        }

        $media = collect();

        // Get profile media
        if ($this->profile) {
            $profilePictures = $this->profile->getMedia('profile_pictures');
            $coverPhotos = $this->profile->getMedia('cover_photos');
            $profileMedia = $profilePictures->merge($coverPhotos);

            $media = $media->merge($profileMedia->map(function ($item) {
                return [
                    'id' => $item->id,
                    'url' => $item->getUrl(),
                    'thumb_url' => $item->getUrl('profile_medium') ?? $item->getUrl('cover_medium') ?? $item->getUrl(),
                    'name' => $item->name,
                    'collection' => $item->collection_name,
                    'created_at' => $item->created_at,
                    'type' => 'profile',
                ];
            }));
        }

        // Get status media
        $statusMedia = Status::where('user_id', $this->user->id)
            ->whereHas('media')
            ->with('media')
            ->get()
            ->pluck('media')
            ->flatten();

        $media = $media->merge($statusMedia->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->getUrl(),
                'thumb_url' => $item->getUrl('status_medium') ?? $item->getUrl(),
                'name' => $item->name,
                'collection' => $item->collection_name,
                'created_at' => $item->created_at,
                'type' => 'status',
            ];
        }));

        // Get task completion media
        $taskMedia = UserAssignedTask::where('user_id', $this->user->id)
            ->whereHas('media')
            ->with('media')
            ->get()
            ->pluck('media')
            ->flatten();

        $media = $media->merge($taskMedia->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->getUrl(),
                'thumb_url' => $item->getUrl('medium') ?? $item->getUrl(),
                'name' => $item->name,
                'collection' => $item->collection_name,
                'created_at' => $item->created_at,
                'type' => 'task',
            ];
        }));

        return $media->sortByDesc('created_at');
    }

    public function render()
    {
        return view('livewire.user.social-profile.media-tab');
    }
}
