<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the trial period for new users. This is the number of days
    | that new users get for free before they must choose a subscription plan.
    |
    */

    'trial_days' => env('SUBSCRIPTION_TRIAL_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Stripe Price IDs
    |--------------------------------------------------------------------------
    |
    | These are the Stripe price IDs for each subscription plan. You need to
    | create these in your Stripe dashboard and update them here.
    |
    | For testing, you can use Stripe's test price IDs or create your own.
    |
    */

    'prices' => [
        'solo' => (float) env('STRIPE_PRICE_SOLO', 1.99),
        'premium' => (float) env('STRIPE_PRICE_PREMIUM', 2.99),
        'couple' => (float) env('STRIPE_PRICE_COUPLE', 3.99),
        'lifetime' => (float) env('STRIPE_PRICE_LIFETIME', 99.99),
    ],

    'stripe_prices' => [
        'solo' => env('STRIPE_PRICE_ID_SOLO', 'price_solo_monthly'),
        'premium' => env('STRIPE_PRICE_ID_PREMIUM', 'price_premium_monthly'),
        'couple' => env('STRIPE_PRICE_ID_COUPLE', 'price_couple_monthly'),
        'lifetime' => env('STRIPE_PRICE_ID_LIFETIME', 'price_lifetime'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Product IDs
    |--------------------------------------------------------------------------
    |
    | These are the Stripe product IDs for each subscription plan. You need to
    | create these in your Stripe dashboard and update them here.
    |
    */

    'stripe_products' => [
        'solo' => env('STRIPE_PRODUCT_SOLO', 'prod_solo'),
        'premium' => env('STRIPE_PRODUCT_PREMIUM', 'prod_premium'),
        'couple' => env('STRIPE_PRODUCT_COUPLE', 'prod_couple'),
        'lifetime' => env('STRIPE_PRODUCT_LIFETIME', 'prod_lifetime'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Limits
    |--------------------------------------------------------------------------
    |
    | Configure limits for different subscription tiers.
    |
    */

    'limits' => [
        'free' => [
            'tasks_per_day' => 1,
            'can_create_stories' => false,
            'can_upload_images' => false,
            'can_access_premium_content' => false,
            'can_create_custom_tasks' => false,
        ],
        'solo' => [
            'tasks_per_day' => null, // Unlimited
            'can_create_stories' => true,
            'can_upload_images' => false,
            'can_access_premium_content' => false,
            'can_create_custom_tasks' => false,
        ],
        'premium' => [
            'tasks_per_day' => null, // Unlimited
            'can_create_stories' => true,
            'can_upload_images' => true,
            'can_access_premium_content' => true,
            'can_create_custom_tasks' => true,
        ],
        'couple' => [
            'tasks_per_day' => null, // Unlimited
            'can_create_stories' => true,
            'can_upload_images' => true,
            'can_access_premium_content' => true,
            'can_create_custom_tasks' => true,
        ],
        'lifetime' => [
            'tasks_per_day' => null, // Unlimited
            'can_create_stories' => true,
            'can_upload_images' => true,
            'can_access_premium_content' => true,
            'can_create_custom_tasks' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook endpoints for Stripe events.
    |
    */

    'webhooks' => [
        'enabled' => env('STRIPE_WEBHOOKS_ENABLED', true),
        'endpoint' => env('STRIPE_WEBHOOK_ENDPOINT', '/stripe/webhook'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    |
    | Configure redirect URLs for subscription flows.
    |
    */

    'redirects' => [
        'success' => env('SUBSCRIPTION_SUCCESS_URL', '/app/subscription/success'),
        'cancel' => env('SUBSCRIPTION_CANCEL_URL', '/app/subscription/cancel'),
        'billing' => env('SUBSCRIPTION_BILLING_URL', '/app/subscription/billing'),
    ],
];
