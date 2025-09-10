<?php

use App\Models\Fantasy;
use App\Models\User;
use App\ContentStatus;
use Database\Seeders\FantasySeeder;

it('creates 25 fantasies when seeded', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(FantasySeeder::class);
    
    // Assert that 25 fantasies were created
    expect(Fantasy::count())->toBe(25);
});

it('creates fantasies with approved status', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(FantasySeeder::class);
    
    // Assert that all fantasies have approved status
    expect(Fantasy::where('status', ContentStatus::Approved->value)->count())->toBe(25);
});

it('creates fantasies with mixed premium status', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(FantasySeeder::class);
    
    // Assert that we have both premium and non-premium fantasies
    $premiumCount = Fantasy::where('is_premium', true)->count();
    $nonPremiumCount = Fantasy::where('is_premium', false)->count();
    
    expect($premiumCount)->toBeGreaterThan(0);
    expect($nonPremiumCount)->toBeGreaterThan(0);
    expect($premiumCount + $nonPremiumCount)->toBe(25);
});

it('assigns fantasies to existing users', function () {
    // Create test users first
    $users = User::factory()->count(3)->create();
    
    // Run the seeder
    $this->seed(FantasySeeder::class);
    
    // Assert that all fantasies have a user assigned
    expect(Fantasy::whereNotNull('user_id')->count())->toBe(25);
    
    // Assert that fantasies are distributed among users
    $userIds = Fantasy::pluck('user_id')->unique();
    expect($userIds->count())->toBeGreaterThan(1);
});