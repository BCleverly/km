<?php

namespace App\Livewire\User;

use App\Livewire\Forms\User\ProfileForm;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Profile extends Component
{
    public ProfileForm $form;

    public function mount()
    {
        $user = auth()->user();
        $this->form->name = $user->name;
        $this->form->email = $user->email;
        $this->form->username = $user->username ?? '';
        $this->form->about = $user->about ?? '';
    }

    public function save()
    {
        $user = auth()->user();
        
        // Custom validation for username uniqueness
        $this->form->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'about' => 'nullable|string|max:1000',
        ]);
        
        $user->update($this->form->only(['name', 'email', 'username', 'about']));
        
        $this->dispatch('profile-updated');
        session()->flash('message', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.profile')
            ->layout('components.layouts.app', [
                'title' => 'Profile - Kink Master'
            ]);
    }
}
