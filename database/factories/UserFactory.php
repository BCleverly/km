<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => \App\TargetUserType::Any,
            'subscription_plan' => \App\Enums\SubscriptionPlan::Free,
            'subscription_ends_at' => null,
            'has_used_trial' => false,
            'trial_ends_at' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $user->profile()->create([
                'username' => fake()->unique()->userName(),
                'about' => fake()->optional(0.7)->paragraph(),
            ]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with a trial subscription.
     */
    public function onTrial(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => \App\Enums\SubscriptionPlan::Free,
            'trial_ends_at' => now()->addDays(14),
            'has_used_trial' => true,
        ]);
    }

    /**
     * Create a user with a paid subscription.
     */
    public function withPaidSubscription(\App\Enums\SubscriptionPlan $plan = \App\Enums\SubscriptionPlan::Solo): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => $plan,
            'subscription_ends_at' => now()->addMonth(),
            'has_used_trial' => true,
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Create a user with a lifetime subscription.
     */
    public function withLifetimeSubscription(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => \App\Enums\SubscriptionPlan::Lifetime,
            'subscription_ends_at' => null,
            'has_used_trial' => true,
            'trial_ends_at' => null,
        ]);
    }
}
