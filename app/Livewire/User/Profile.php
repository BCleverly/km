<?php

namespace App\Livewire\User;

use App\Livewire\Forms\User\ProfileForm;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public ProfileForm $form;

    public function mount()
    {
        // Initialize form with user data
        $this->form->initializeWithUserData();
    }

    public function save()
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Custom validation for username uniqueness and file uploads
        $this->form->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|unique:profiles,username,'.($profile?->id ?? 'NULL'),
            'about' => 'nullable|string|max:1000',
            'theme_preference' => 'required|string|in:light,dark,system',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        // Update user basic information
        $user->update($this->form->only(['name', 'email']));

        // Create or update profile
        if (! $profile) {
            $profile = $user->profile()->create([
                'username' => $this->form->username,
                'about' => $this->form->about,
                'theme_preference' => $this->form->theme_preference,
            ]);
        } else {
            $profile->update([
                'username' => $this->form->username,
                'about' => $this->form->about,
                'theme_preference' => $this->form->theme_preference,
            ]);
        }

        // Handle profile picture upload
        if ($this->form->profile_picture && $this->form->profile_picture->isValid()) {
            $profile->clearMediaCollection('profile_pictures');
            $profile->addMedia($this->form->profile_picture->getRealPath())
                ->usingName($this->form->profile_picture->getClientOriginalName())
                ->toMediaCollection('profile_pictures');

            // Clear the form field after successful upload
            $this->form->profile_picture = null;
        }

        // Handle cover photo upload
        if ($this->form->cover_photo && $this->form->cover_photo->isValid()) {
            $profile->clearMediaCollection('cover_photos');
            $profile->addMedia($this->form->cover_photo->getRealPath())
                ->usingName($this->form->cover_photo->getClientOriginalName())
                ->toMediaCollection('cover_photos');

            // Clear the form field after successful upload
            $this->form->cover_photo = null;
        }

        $this->dispatch('profile-updated');
        session()->flash('message', 'Profile updated successfully!');
    }

    public function removeProfilePicture()
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($profile) {
            $profile->clearMediaCollection('profile_pictures');
        }

        $this->dispatch('profile-updated');
        session()->flash('message', 'Profile picture removed successfully!');
    }

    public function removeCoverPhoto()
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($profile) {
            $profile->clearMediaCollection('cover_photos');
        }

        $this->dispatch('profile-updated');
        session()->flash('message', 'Cover photo removed successfully!');
    }

    public function canPreviewFile($file)
    {
        if (! $file || ! $file->isValid()) {
            return false;
        }

        try {
            // Try to get the extension
            $extension = $file->getClientOriginalExtension();
            if (empty($extension)) {
                return false;
            }

            // Check if it's in the allowed preview mimes
            $allowedMimes = config('livewire.temporary_file_upload.preview_mimes', []);

            return in_array(strtolower($extension), $allowedMimes);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCoverPhotoPreviewUrl()
    {
        if ($this->form->cover_photo && $this->canPreviewFile($this->form->cover_photo)) {
            try {
                return $this->form->cover_photo->temporaryUrl();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    public function getProfilePicturePreviewUrl()
    {
        if ($this->form->profile_picture && $this->canPreviewFile($this->form->profile_picture)) {
            try {
                return $this->form->profile_picture->temporaryUrl();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    public function getStoredCoverPhotoUrl()
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($profile) {
            $media = $profile->getFirstMedia('cover_photos');
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

    public function getStoredProfilePictureUrl()
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($profile) {
            $media = $profile->getFirstMedia('profile_pictures');
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
        return $user->gravatar_url;
    }

    public function isProfilePictureConversionReady()
    {
        $user = auth()->user();
        $profile = $user->profile;

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

        return true; // No media means no conversion needed
    }

    public function isCoverPhotoConversionReady()
    {
        $user = auth()->user();
        $profile = $user->profile;

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

        return true; // No media means no conversion needed
    }

    public function checkConversions()
    {
        // This method can be called to refresh the component state
        // and check if conversions are ready
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.profile')
            ->layout('components.layouts.app', [
                'title' => 'Profile - Kink Master',
            ]);
    }
}
