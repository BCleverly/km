<?php

use App\Models\Story;
use App\Models\User;
use App\ContentStatus;
use Database\Seeders\StorySeeder;

it('creates 25 stories when seeded', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that 25 stories were created
    expect(Story::count())->toBe(25);
});

it('creates stories with approved status', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that all stories have approved status
    expect(Story::where('status', ContentStatus::Approved->value)->count())->toBe(25);
});

it('creates stories with non-premium status', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that all stories are non-premium
    $premiumCount = Story::where('is_premium', true)->count();
    $nonPremiumCount = Story::where('is_premium', false)->count();
    
    expect($premiumCount)->toBe(0);
    expect($nonPremiumCount)->toBe(25);
});

it('assigns stories to existing users', function () {
    // Create test users first
    $users = User::factory()->count(3)->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that all stories have a user assigned
    expect(Story::whereNotNull('user_id')->count())->toBe(25);
    
    // Assert that stories are distributed among users
    $userIds = Story::pluck('user_id')->unique();
    expect($userIds->count())->toBeGreaterThan(1);
});

it('creates stories with proper titles and summaries', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that all stories have titles and summaries
    expect(Story::whereNotNull('title')->count())->toBe(25);
    expect(Story::whereNotNull('summary')->count())->toBe(25);
    
    // Assert that titles and summaries are not empty
    expect(Story::where('title', '!=', '')->count())->toBe(25);
    expect(Story::where('summary', '!=', '')->count())->toBe(25);
});

it('generates unique slugs for stories', function () {
    // Create a test user first
    $user = User::factory()->create();
    
    // Run the seeder
    $this->seed(StorySeeder::class);
    
    // Assert that all stories have slugs
    expect(Story::whereNotNull('slug')->count())->toBe(25);
    
    // Assert that all slugs are unique
    $slugs = Story::pluck('slug');
    expect($slugs->count())->toBe($slugs->unique()->count());
});