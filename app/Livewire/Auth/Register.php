<?php

namespace App\Livewire\Auth;

use App\Enums\SubscriptionPlan;
use App\Livewire\Forms\RegisterForm;
use App\Models\User;
use App\Services\SubscriptionService;
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
            'subscription_plan' => $this->form->subscription_plan,
        ]);

        // Handle subscription based on selected plan
        $selectedPlan = SubscriptionPlan::from($this->form->subscription_plan);
        
        if ($selectedPlan === SubscriptionPlan::Free) {
            // Start the trial period for free plan users
            $user->startTrial();
        } else {
            // For paid plans, start trial and redirect to Stripe checkout
            $user->startTrial();
            $user->updateSubscriptionPlan($selectedPlan);
        }

        event(new Registered($user));
        Auth::login($user);

        // If user selected a paid plan, redirect to Stripe checkout
        if ($selectedPlan !== SubscriptionPlan::Free) {
            $checkoutUrl = app(SubscriptionService::class)->createCheckoutSession($user, $selectedPlan);
            return $this->redirect($checkoutUrl);
        }

        return $this->redirect(route('app.dashboard'), navigate: true);
    }

    public function getPlansProperty()
    {
        return [
            SubscriptionPlan::Free,
            SubscriptionPlan::Solo,
            SubscriptionPlan::Premium,
            SubscriptionPlan::Couple,
            SubscriptionPlan::Lifetime,
        ];
    }

    public function render()
    {
        return view('livewire.register')
            ->layout('components.layouts.guest', [
                'title' => 'Get started for free - Kink Master',
            ]);
    }
}
