<?php

namespace App\Livewire\Forms\User;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfileForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $username = '';

    #[Validate('nullable|string|max:1000')]
    public string $about = '';

    #[Validate('required|string|in:light,dark,system')]
    public string $theme_preference = 'system';

    public $profile_picture;

    public $cover_photo;

    public function initializeWithUserData()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $profile = $user->profile;

            $this->name = $user->name;
            $this->email = $user->email;
            $this->username = $profile?->username ?? '';
            $this->about = $profile?->about ?? '';
            $this->theme_preference = $profile?->theme_preference ?? 'system';
        }
    }
}
