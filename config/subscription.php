<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | This configuration defines the available subscription plans for the
    | Kink Master platform. Each plan has its own pricing, features, and
    | Stripe integration settings.
    |
    */

    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'currency' => 'gbp',
            'interval' => null,
            'trial_days' => 0,
            'features' => [
                'Limited task assignments',
                'Basic rewards and punishments',
                'Community access',
            ],
        ],
        'monthly' => [
            'name' => 'Monthly',
            'price' => 299, // £2.99 in pence
            'currency' => 'gbp',
            'interval' => 'month',
            'trial_days' => 14,
            'features' => [
                'Unlimited task assignments',
                'Full rewards and punishments library',
                'Priority support',
                'Advanced analytics',
                'Custom task creation',
            ],
        ],
        'couple' => [
            'name' => 'Couple',
            'price' => 399, // £3.99 in pence
            'currency' => 'gbp',
            'interval' => 'month',
            'trial_days' => 14,
            'features' => [
                'Everything in Monthly',
                'Shared couple dashboard',
                'Partner task assignments',
                'Couple-specific content',
                'Joint progress tracking',
            ],
        ],
        'lifetime' => [
            'name' => 'Lifetime',
            'price' => 9900, // £99.00 in pence
            'currency' => 'gbp',
            'interval' => null,
            'trial_days' => 0,
            'features' => [
                'Everything in Couple',
                'No recurring payments',
                'Lifetime updates',
                'Premium support',
                'Early access to new features',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Stripe price IDs for each subscription plan. These should be created
    | in your Stripe dashboard and configured here.
    |
    */

    'stripe_prices' => [
        'monthly' => env('STRIPE_PRICE_MONTHLY'),
        'couple' => env('STRIPE_PRICE_COUPLE'),
        'lifetime' => env('STRIPE_PRICE_LIFETIME'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    |
    | Default trial settings for new subscriptions.
    |
    */

    'trial_days' => 14,

    /*
    |--------------------------------------------------------------------------
    | Feature Limits
    |--------------------------------------------------------------------------
    |
    | Define feature limits for each subscription tier.
    |
    */

    'limits' => [
        'free' => [
            'max_tasks_per_day' => 3,
            'max_active_outcomes' => 1,
            'can_create_content' => false,
            'can_access_premium_content' => false,
        ],
        'monthly' => [
            'max_tasks_per_day' => null, // unlimited
            'max_active_outcomes' => 5,
            'can_create_content' => true,
            'can_access_premium_content' => true,
        ],
        'couple' => [
            'max_tasks_per_day' => null, // unlimited
            'max_active_outcomes' => 10,
            'can_create_content' => true,
            'can_access_premium_content' => true,
            'can_assign_partner_tasks' => true,
        ],
        'lifetime' => [
            'max_tasks_per_day' => null, // unlimited
            'max_active_outcomes' => null, // unlimited
            'can_create_content' => true,
            'can_access_premium_content' => true,
            'can_assign_partner_tasks' => true,
            'has_premium_support' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Stripe webhook events that should be handled for subscription management.
    |
    */

    'webhook_events' => [
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted',
        'invoice.payment_succeeded',
        'invoice.payment_failed',
        'customer.subscription.trial_will_end',
    ],
];