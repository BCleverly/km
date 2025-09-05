<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('required|string|email|max:255|unique:users')]
    public string $email = '';

    #[Validate('required|password')]
    public string $password = '';

    #[Validate('required|string|in:search,advertisement,referral,other')]
    public string $hear_about = '';
}
