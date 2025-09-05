<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ForgotPasswordForm extends Form
{
    #[Validate('required|email')]
    public string $email = '';
}
