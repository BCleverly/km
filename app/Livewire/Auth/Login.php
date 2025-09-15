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

        if (! Auth::attempt($this->form->only(['email', 'password']), $this->form->remember)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        return $this->redirect(route('app.dashboard'), navigate: true);
    }

    public function quickLogin(string $role)
    {
        // Only allow in local/development environments
        if (! app()->environment(['local', 'development'])) {
            abort(403, 'Quick login is only available in development environments.');
        }

        $userEmails = [
            'admin' => 'admin@example.com',
            'moderator' => 'moderator@example.com',
            'reviewer' => 'reviewer@example.com',
            'user' => 'test@example.com',
            'couple1' => 'couple1@example.com',
            'couple2' => 'couple2@example.com',
        ];

        if (! isset($userEmails[$role])) {
            throw ValidationException::withMessages([
                'role' => 'Invalid role specified.',
            ]);
        }

        $user = \App\Models\User::where('email', $userEmails[$role])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'role' => 'User for this role not found. Please run database seeder.',
            ]);
        }

        Auth::login($user, true);
        session()->regenerate();

        return $this->redirect(route('app.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.guest', [
                'title' => 'Sign in to your account - Kink Master',
            ]);
    }
}
