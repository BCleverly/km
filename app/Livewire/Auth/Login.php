<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public LoginForm $form;

    public function login()
    {
        $this->form->validate();

        if (!Auth::attempt($this->form->all(), $this->form->remember)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        return $this->redirect('/dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.guest', [
                'title' => 'Sign in to your account - Kink Master'
            ]);
    }
}