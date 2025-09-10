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
    
    $viewCount = $service->getViewCount('fantasy', $fantasy->id);
    expect($viewCount)->toBe(1);
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