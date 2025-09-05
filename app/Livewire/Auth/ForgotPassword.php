<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\ForgotPasswordForm;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public ForgotPasswordForm $form;

    public bool $emailSent = false;

    public function sendResetLink()
    {
        $this->form->validate();

        $status = Password::sendResetLink(
            $this->form->only(['email'])
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
        } else {
            $this->form->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.forgot-password')
            ->layout('components.layouts.guest', [
                'title' => 'Forgot Password - Kink Master'
            ]);
    }
}