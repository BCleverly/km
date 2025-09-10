<?php

declare(strict_types=1);

use App\Livewire\Components\ReactionButton;
use App\Models\Tasks\Task;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('caches reaction summary data', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    $this->actingAs($user);
    
    // Clear any existing cache
    Cache::flush();
    
    // First call should hit the database and cache the result
    $component = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task->id,
    ]);
    
    $reactions = $component->instance()->getReactions();
    
    // Verify cache key exists
    $cacheKey = 'reactions_' . Task::class . '_' . $task->id . '_summary';
    expect(Cache::has($cacheKey))->toBeTrue();
    
    // Second call should use cached data
    $cachedReactions = $component->instance()->getReactions();
    expect($cachedReactions)->toEqual($reactions);
});

it('caches user reaction data', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    $this->actingAs($user);
    
    // Clear any existing cache
    Cache::flush();
    
    // First call should hit the database and cache the result
    $component = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task->id,
    ]);
    
    $userReaction = $component->instance()->getUserReaction();
    
    // Verify cache key exists
    $cacheKey = 'reactions_' . Task::class . '_' . $task->id . '_user_' . $user->id;
    expect(Cache::has($cacheKey))->toBeTrue();
    
    // Second call should use cached data
    $cachedUserReaction = $component->instance()->getUserReaction();
    expect($cachedUserReaction)->toEqual($userReaction);
});

it('clears cache when reaction is added', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    $this->actingAs($user);
    
    // Clear any existing cache
    Cache::flush();
    
    $component = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task->id,
    ]);
    
    // Generate cache by calling the methods
    $component->instance()->getReactions();
    $component->instance()->getUserReaction();
    
    // Verify cache exists
    $summaryCacheKey = 'reactions_' . Task::class . '_' . $task->id . '_summary';
    $userCacheKey = 'reactions_' . Task::class . '_' . $task->id . '_user_' . $user->id;
    
    expect(Cache::has($summaryCacheKey))->toBeTrue();
    expect(Cache::has($userCacheKey))->toBeTrue();
    
    // Add a reaction
    $component->call('addReaction', 'like');
    
    // Verify cache is cleared
    expect(Cache::has($summaryCacheKey))->toBeFalse();
    expect(Cache::has($userCacheKey))->toBeFalse();
});

it('clears cache when reaction is removed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    $this->actingAs($user);
    
    // Add a reaction first
    $user->reactTo($task, 'like');
    
    // Clear any existing cache
    Cache::flush();
    
    $component = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task->id,
    ]);
    
    // Generate cache by calling the methods
    $component->instance()->getReactions();
    $component->instance()->getUserReaction();
    
    // Verify cache exists
    $summaryCacheKey = 'reactions_' . Task::class . '_' . $task->id . '_summary';
    $userCacheKey = 'reactions_' . Task::class . '_' . $task->id . '_user_' . $user->id;
    
    expect(Cache::has($summaryCacheKey))->toBeTrue();
    expect(Cache::has($userCacheKey))->toBeTrue();
    
    // Remove the reaction
    $component->call('removeReaction');
    
    // Verify cache is cleared
    expect(Cache::has($summaryCacheKey))->toBeFalse();
    expect(Cache::has($userCacheKey))->toBeFalse();
});

it('generates unique cache keys for different models', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create();
    $task2 = Task::factory()->create();
    
    $this->actingAs($user);
    
    $component1 = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task1->id,
    ]);
    
    $component2 = Livewire::test(ReactionButton::class, [
        'modelType' => 'task',
        'modelId' => $task2->id,
    ]);
    
    $cacheKey1 = $component1->instance()->getCacheKey('summary');
    $cacheKey2 = $component2->instance()->getCacheKey('summary');
    
    expect($cacheKey1)->not->toBe($cacheKey2);
    expect($cacheKey1)->toContain((string) $task1->id);
    expect($cacheKey2)->toContain((string) $task2->id);
});