<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\RegisterForm;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public RegisterForm $form;

    public function register()
    {
        $this->form->validate();

        $user = User::create([
            'name' => trim($this->form->first_name.' '.$this->form->last_name),
            'username' => $this->form->username,
            'email' => $this->form->email,
            'password' => Hash::make($this->form->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $this->redirect(route('app.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.register')
            ->layout('components.layouts.guest', [
                'title' => 'Get started for free - Kink Master',
            ]);
    }
}
