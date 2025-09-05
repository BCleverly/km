<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\ResetPasswordForm;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public ResetPasswordForm $form;

    public bool $passwordReset = false;

    public function mount(string $token)
    {
        $this->form->token = $token;
        $this->form->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->form->validate();

        $status = Password::reset(
            $this->form->all(),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->passwordReset = true;
        } else {
            $this->form->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.reset-password')
            ->layout('components.layouts.guest', [
                'title' => 'Reset Password - Kink Master'
            ]);
    }
}