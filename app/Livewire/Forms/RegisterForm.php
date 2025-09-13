<?php

namespace App\Livewire\Forms;

use App\Enums\SubscriptionPlan;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('required|string|max:255|unique:users|alpha_dash')]
    public string $username = '';

    #[Validate('required|string|email|max:255|unique:users')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('nullable|string|in:search,advertisement,referral,other')]
    public ?string $hear_about = null;

    #[Validate('required|integer')]
    public ?int $subscription_plan = SubscriptionPlan::Free->value;
}
