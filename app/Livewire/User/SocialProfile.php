<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Livewire\Forms\User\ProfileForm;
use App\Models\Profile;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SocialProfile extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?string $username = null;

    public ProfileForm $form;

    public bool $isEditing = false;

    public bool $showEditForm = false;


    public function mount(?string $username = null): void
    {
        $this->username = $username;

        if ($this->isOwnProfile) {
            $this->form->initializeWithUserData();
        }
    }

    #[Computed]
    public function isOwnProfile(): bool
    {
        if (! $this->username) {
            return true; // Current user's own profile
        }

        return auth()->check() && auth()->user()->profile?->username === $this->username;
    }

    #[Computed]
    public function profile(): ?Profile
    {
        if ($this->isOwnProfile) {
            return auth()->user()?->profile;
        }

        return Profile::with(['user', 'media'])->where('username', $this->username)->first();
    }

    #[Computed]
    public function user(): ?User
    {
        return $this->profile?->user;
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
    public function joinedDate(): string
    {
        return $this->user?->created_at->format('F Y') ?? '';
    }

    #[Computed]
    public function profilePictureUrl(): string
    {
        if ($this->profile && $this->profile->relationLoaded('media')) {
            $media = $this->profile->getFirstMedia('profile_pictures');
            if ($media) {
                try {
                    return $media->getUrl('profile_medium');
                } catch (\Exception $e) {
                    return $media->getUrl();
                }
            }
        }

        return $this->user?->gravatar_url ?? '';
    }

    #[Computed]
    public function coverPhotoUrl(): ?string
    {
        if ($this->profile && $this->profile->relationLoaded('media')) {
            $media = $this->profile->getFirstMedia('cover_photos');
            if ($media) {
                try {
                    return $media->getUrl('cover_medium');
                } catch (\Exception $e) {
                    return $media->getUrl();
                }
            }
        }

        return null;
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

    public function toggleEditForm(): void
    {
        if (! $this->isOwnProfile) {
            return;
        }

        $this->showEditForm = ! $this->showEditForm;

        if ($this->showEditForm) {
            $this->form->initializeWithUserData();
        }
    }

    public function save(): void
    {
        if (! $this->isOwnProfile) {
            return;
        }

        $user = auth()->user();
        $profile = $user->profile;

        // Custom validation for username uniqueness and file uploads
        $this->form->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|unique:profiles,username,'.($profile?->id ?? 'NULL'),
            'about' => 'nullable|string|max:1000',
            'bdsm_role' => 'nullable|integer|in:1,2,3',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Update user basic information
        $user->update($this->form->only(['name', 'email']));

        // Create or update profile
        if (! $profile) {
            $profile = $user->profile()->create([
                'username' => $this->form->username,
                'about' => $this->form->about,
                'bdsm_role' => $this->form->bdsm_role,
            ]);
        } else {
            $profile->update([
                'username' => $this->form->username,
                'about' => $this->form->about,
                'bdsm_role' => $this->form->bdsm_role,
            ]);
        }

        // Handle profile picture upload
        if ($this->form->profile_picture && $this->form->profile_picture->isValid()) {
            $profile->clearMediaCollection('profile_pictures');
            $profile->addMedia($this->form->profile_picture->getRealPath())
                ->usingName($this->form->profile_picture->getClientOriginalName())
                ->usingFileName($this->form->profile_picture->getClientOriginalName())
                ->toMediaCollection('profile_pictures');
        }

        // Handle cover photo upload
        if ($this->form->cover_photo && $this->form->cover_photo->isValid()) {
            $profile->clearMediaCollection('cover_photos');
            $profile->addMedia($this->form->cover_photo->getRealPath())
                ->usingName($this->form->cover_photo->getClientOriginalName())
                ->usingFileName($this->form->cover_photo->getClientOriginalName())
                ->toMediaCollection('cover_photos');
        }

        $this->showEditForm = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Profile updated successfully!',
        ]);
    }

    public function removeProfilePicture(): void
    {
        if (! $this->isOwnProfile) {
            return;
        }

        $profile = auth()->user()->profile;
        if ($profile) {
            $profile->clearMediaCollection('profile_pictures');
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Profile picture removed successfully!',
        ]);
    }

    public function removeCoverPhoto(): void
    {
        if (! $this->isOwnProfile) {
            return;
        }

        $profile = auth()->user()->profile;
        if ($profile) {
            $profile->clearMediaCollection('cover_photos');
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Cover photo removed successfully!',
        ]);
    }

    public function getProfilePicturePreviewUrl(): ?string
    {
        if ($this->form->profile_picture && $this->form->profile_picture->isValid()) {
            return $this->form->profile_picture->temporaryUrl();
        }

        return null;
    }

    public function getCoverPhotoPreviewUrl(): ?string
    {
        if ($this->form->cover_photo && $this->form->cover_photo->isValid()) {
            return $this->form->cover_photo->temporaryUrl();
        }

        return null;
    }

    public function getStoredProfilePictureUrl(): ?string
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $media = $profile->getFirstMedia('profile_pictures');
            if ($media) {
                try {
                    return $media->getUrl('profile_medium');
                } catch (\Exception $e) {
                    return $media->getUrl();
                }
            }
        }

        return null;
    }

    public function getStoredCoverPhotoUrl(): ?string
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $media = $profile->getFirstMedia('cover_photos');
            if ($media) {
                try {
                    return $media->getUrl('cover_medium');
                } catch (\Exception $e) {
                    return $media->getUrl();
                }
            }
        }

        return null;
    }

    public function isProfilePictureConversionReady(): bool
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $media = $profile->getFirstMedia('profile_pictures');
            if ($media) {
                try {
                    $media->getUrl('profile_medium');

                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        return true;
    }

    public function isCoverPhotoConversionReady(): bool
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $media = $profile->getFirstMedia('cover_photos');
            if ($media) {
                try {
                    $media->getUrl('cover_medium');

                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        return true;
    }

    public function checkConversions(): void
    {
        // This method is called periodically to check if media conversions are ready
        // The UI will automatically update when the computed properties return the converted URLs
    }


    #[On('status-created')]
    public function handleStatusCreated(): void
    {
        // Refresh any computed properties that might be affected by new statuses
        $this->dispatch('$refresh');
    }

    #[On('status-deleted')]
    public function handleStatusDeleted(): void
    {
        // Refresh any computed properties that might be affected by deleted statuses
        $this->dispatch('$refresh');
    }

    public function getBdsmRoleOptions(): array
    {
        return \App\Enums\BdsmRole::options();
    }

    public function render()
    {
        return view('livewire.user.social-profile')
            ->layout('components.layouts.app', [
                'title' => $this->isOwnProfile ? 'My Profile - Kink Master' : $this->displayName.' - Kink Master',
            ]);
    }
}
