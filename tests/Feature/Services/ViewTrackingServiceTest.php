<?php

use App\Services\ViewTrackingService;
use App\Models\User;
use App\Models\Fantasy;
use App\Models\Story;
use Illuminate\Support\Facades\Redis;

beforeEach(function () {
    // Clear Redis before each test
    Redis::flushdb();
});

it('can track views for fantasies', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // First view should be tracked
    $result = $service->trackView('fantasy', $fantasy->id, $user->id);
    expect($result)->toBeTrue();
    
    // View count should be database count (0) + Redis count (1) = 1
    $viewCount = $service->getViewCount('fantasy', $fantasy->id);
    expect($viewCount)->toBe(1);
    
    // Redis count should be 1
    $redisCount = $service->getRedisViewCount('fantasy', $fantasy->id);
    expect($redisCount)->toBe(1);
});

it('can track views for stories', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // First view should be tracked
    $result = $service->trackView('story', $story->id, $user->id);
    expect($result)->toBeTrue();
    
    $viewCount = $service->getViewCount('story', $story->id);
    expect($viewCount)->toBe(1);
});

it('prevents abuse by limiting views per session', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // Track maximum allowed views per session
    for ($i = 0; $i < 3; $i++) {
        $result = $service->trackView('fantasy', $fantasy->id, $user->id);
        expect($result)->toBeTrue();
    }
    
    // Next view should be blocked
    $result = $service->trackView('fantasy', $fantasy->id, $user->id);
    expect($result)->toBeFalse();
    
    $viewCount = $service->getViewCount('fantasy', $fantasy->id);
    expect($viewCount)->toBe(3);
});

it('prevents abuse by limiting daily views per user', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // Track maximum allowed daily views (100)
    for ($i = 0; $i < 100; $i++) {
        $result = $service->trackView('fantasy', $fantasy->id, $user->id);
        expect($result)->toBeTrue();
    }
    
    // Next view should be blocked due to daily limit
    $result = $service->trackView('fantasy', $fantasy->id, $user->id);
    expect($result)->toBeFalse();
});

it('enforces cooldown period between views', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // First view should be tracked
    $result = $service->trackView('fantasy', $fantasy->id, $user->id);
    expect($result)->toBeTrue();
    
    // Immediate second view should be blocked by cooldown
    $result = $service->trackView('fantasy', $fantasy->id, $user->id);
    expect($result)->toBeFalse();
});

it('prevents multiple views of same content per day', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // First view should be tracked
    $result = $service->trackView('story', $story->id, $user->id);
    expect($result)->toBeTrue();
    
    // Wait for cooldown to expire (simulate by clearing cooldown key)
    $cooldownKey = 'cooldown:user:' . $user->id . ':story:' . $story->id;
    Redis::del($cooldownKey);
    
    // Second view should still be blocked because user already viewed today
    $result = $service->trackView('story', $story->id, $user->id);
    expect($result)->toBeFalse();
    
    // View count should still be 1
    $viewCount = $service->getViewCount('story', $story->id);
    expect($viewCount)->toBe(1);
});

it('can get view counts for multiple models', function () {
    $user = User::factory()->create();
    $fantasy1 = Fantasy::factory()->create();
    $fantasy2 = Fantasy::factory()->create();
    $story = Story::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // Track some views
    $service->trackView('fantasy', $fantasy1->id, $user->id);
    $service->trackView('fantasy', $fantasy1->id, $user->id);
    $service->trackView('fantasy', $fantasy2->id, $user->id);
    $service->trackView('story', $story->id, $user->id);
    
    // Get view counts for multiple fantasies
    $viewCounts = $service->getViewCounts('fantasy', [$fantasy1->id, $fantasy2->id]);
    
    expect($viewCounts)->toHaveKey($fantasy1->id);
    expect($viewCounts)->toHaveKey($fantasy2->id);
    expect($viewCounts[$fantasy1->id])->toBe(2);
    expect($viewCounts[$fantasy2->id])->toBe(1);
    
    // Verify Redis keys are scoped properly
    $fantasyKeys = Redis::keys('fantasy:views:*');
    $storyKeys = Redis::keys('story:views:*');
    
    expect($fantasyKeys)->toHaveCount(2);
    expect($storyKeys)->toHaveCount(1);
});

it('can get abuse prevention statistics', function () {
    $service = app(ViewTrackingService::class);
    
    $stats = $service->getAbuseStats();
    
    expect($stats)->toHaveKey('max_views_per_session');
    expect($stats)->toHaveKey('max_daily_views_per_user');
    expect($stats)->toHaveKey('view_cooldown_minutes');
    expect($stats)->toHaveKey('session_duration_hours');
    expect($stats['max_views_per_session'])->toBe(3);
    expect($stats['max_daily_views_per_user'])->toBe(100);
    expect($stats['view_cooldown_minutes'])->toBe(5);
});

it('uses scoped Redis keys for different model types', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create();
    $story = Story::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // Track views for different model types
    $service->trackView('fantasy', $fantasy->id, $user->id);
    $service->trackView('story', $story->id, $user->id);
    
    // Check that keys are properly scoped
    $fantasyKeys = Redis::keys('fantasy:views:*');
    $storyKeys = Redis::keys('story:views:*');
    $allViewKeys = Redis::keys('*:views:*');
    
    expect($fantasyKeys)->toHaveCount(1);
    expect($storyKeys)->toHaveCount(1);
    expect($allViewKeys)->toHaveCount(2);
    
    // Verify key format
    expect($fantasyKeys[0])->toStartWith('fantasy:views:');
    expect($storyKeys[0])->toStartWith('story:views:');
});

it('allows viewing different content on the same day', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create();
    $story2 = Story::factory()->create();
    
    $service = app(ViewTrackingService::class);
    
    // View first story
    $result1 = $service->trackView('story', $story1->id, $user->id);
    expect($result1)->toBeTrue();
    
    // Clear cooldown to simulate time passing
    $cooldownKey1 = 'cooldown:user:' . $user->id . ':story:' . $story1->id;
    Redis::del($cooldownKey1);
    
    // View second story (should be allowed)
    $result2 = $service->trackView('story', $story2->id, $user->id);
    expect($result2)->toBeTrue();
    
    // Both stories should have 1 view each
    expect($service->getViewCount('story', $story1->id))->toBe(1);
    expect($service->getViewCount('story', $story2->id))->toBe(1);
});

it('combines database and Redis view counts correctly', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['view_count' => 50]); // Database has 50 views
    
    $service = app(ViewTrackingService::class);
    
    // Track a new view
    $result = $service->trackView('story', $story->id, $user->id);
    expect($result)->toBeTrue();
    
    // Total view count should be database (50) + Redis (1) = 51
    $totalViewCount = $service->getViewCount('story', $story->id);
    expect($totalViewCount)->toBe(51);
    
    // Redis count should be 1
    $redisCount = $service->getRedisViewCount('story', $story->id);
    expect($redisCount)->toBe(1);
    
    // Database count should still be 50
    $story->refresh();
    expect($story->view_count)->toBe(50);
});