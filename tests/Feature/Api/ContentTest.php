<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Story;
use App\Models\Status;
use App\Models\Fantasy;
use App\ContentStatus;
use App\Enums\SubscriptionPlan;

it('can get stories', function () {
    // Create some approved stories
    Story::factory()->count(3)->create([
        'status' => ContentStatus::Approved,
    ]);

    $response = $this->getJson('/api/v1/content/stories');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'stories' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'summary',
                    'word_count',
                    'reading_time_minutes',
                    'view_count',
                    'created_at',
                    'author' => [
                        'id',
                        'name',
                        'username',
                    ],
                    'tags',
                    'reactions' => [
                        'count',
                        'user_reacted',
                    ],
                ],
            ],
            'pagination',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('stories'))->toHaveCount(3);
});

it('can get specific story by slug', function () {
    $story = Story::factory()->create([
        'title' => 'Test Story',
        'slug' => 'test-story',
        'status' => ContentStatus::Approved,
    ]);

    $response = $this->getJson("/api/v1/content/stories/{$story->slug}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'story' => [
                'id',
                'title',
                'slug',
                'summary',
                'content',
                'word_count',
                'author',
                'tags',
                'reactions',
                'comments',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('story.title'))->toBe('Test Story');
    expect($response->json('story.slug'))->toBe('test-story');
});

it('returns 404 for non-existent story', function () {
    $response = $this->getJson('/api/v1/content/stories/non-existent');

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Story not found',
        ]);
});

it('can create story with valid subscription', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Premium,
    ]);

    $storyData = [
        'title' => 'My New Story',
        'summary' => 'A brief summary of the story',
        'content' => 'This is the full content of the story. It needs to be at least 100 characters long to pass validation.',
        'tags' => ['BDSM', 'First Time'],
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/stories', $storyData);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'story' => [
                'id',
                'title',
                'slug',
                'summary',
                'status',
                'status_label',
                'created_at',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('story.title'))->toBe('My New Story');
    expect($response->json('story.status'))->toBe(ContentStatus::Pending->value);

    // Verify story was created in database
    $this->assertDatabaseHas('stories', [
        'title' => 'My New Story',
        'user_id' => $user->id,
        'status' => ContentStatus::Pending->value,
    ]);
});

it('cannot create story without valid subscription', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $storyData = [
        'title' => 'My New Story',
        'summary' => 'A brief summary of the story',
        'content' => 'This is the full content of the story. It needs to be at least 100 characters long to pass validation.',
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/stories', $storyData);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Story creation is not available on your current plan',
        ]);
});

it('validates story creation data', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Premium,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/stories', [
            'title' => '', // Empty title
            'summary' => '', // Empty summary
            'content' => 'Short', // Too short content
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => [
                'title',
                'summary',
                'content',
            ],
        ]);
});

it('can get statuses', function () {
    // Create some public statuses
    Status::factory()->count(3)->create([
        'is_public' => true,
    ]);

    $response = $this->getJson('/api/v1/content/statuses');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'statuses' => [
                '*' => [
                    'id',
                    'content',
                    'is_public',
                    'has_image',
                    'status_image_url',
                    'created_at',
                    'user' => [
                        'id',
                        'name',
                        'username',
                        'profile_picture_url',
                    ],
                    'reactions' => [
                        'count',
                        'user_reacted',
                    ],
                    'comments' => [
                        'count',
                    ],
                ],
            ],
            'pagination',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('statuses'))->toHaveCount(3);
});

it('can create status', function () {
    $user = User::factory()->create();

    $statusData = [
        'content' => 'Just completed my task! Feeling great!',
        'is_public' => true,
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/statuses', $statusData);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'status' => [
                'id',
                'content',
                'is_public',
                'has_image',
                'created_at',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('status.content'))->toBe('Just completed my task! Feeling great!');

    // Verify status was created in database
    $this->assertDatabaseHas('statuses', [
        'content' => 'Just completed my task! Feeling great!',
        'user_id' => $user->id,
        'is_public' => true,
    ]);
});

it('can get fantasies', function () {
    // Create some approved fantasies
    Fantasy::factory()->count(3)->create([
        'status' => 2, // Approved
    ]);

    $response = $this->getJson('/api/v1/content/fantasies');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'fantasies' => [
                '*' => [
                    'id',
                    'content',
                    'word_count',
                    'is_premium',
                    'is_anonymous',
                    'view_count',
                    'created_at',
                    'author',
                    'tags',
                    'reactions' => [
                        'count',
                        'user_reacted',
                    ],
                ],
            ],
            'pagination',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('fantasies'))->toHaveCount(3);
});

it('can create fantasy', function () {
    $user = User::factory()->create();

    $fantasyData = [
        'content' => 'I have always fantasized about being dominated in a gentle but firm way. The thought of surrendering control while being cared for is incredibly arousing to me.',
        'is_premium' => false,
        'is_anonymous' => false,
        'tags' => ['BDSM', 'Submission'],
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/fantasies', $fantasyData);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'fantasy' => [
                'id',
                'content',
                'is_premium',
                'is_anonymous',
                'status',
                'status_label',
                'created_at',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('fantasy.content'))->toBe($fantasyData['content']);

    // Verify fantasy was created in database
    $this->assertDatabaseHas('fantasies', [
        'content' => $fantasyData['content'],
        'user_id' => $user->id,
        'is_premium' => false,
        'is_anonymous' => false,
        'status' => 1, // Pending
    ]);
});

it('validates fantasy creation data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/fantasies', [
            'content' => 'Too short', // Too short content
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['content'],
        ]);
});

it('respects daily status limit', function () {
    $user = User::factory()->create();
    
    // Create statuses up to the daily limit
    $maxPerDay = config('app.statuses.max_per_user_per_day', 10);
    Status::factory()->count($maxPerDay)->create([
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/content/statuses', [
            'content' => 'This should be rejected due to daily limit',
            'is_public' => true,
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'You have reached your daily status limit',
        ]);
});