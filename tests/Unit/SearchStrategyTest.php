<?php

declare(strict_types=1);

use App\Contracts\SearchStrategyInterface;
use App\Services\Search\MySqlSearchStrategy;
use App\Models\Story;
use App\Models\User;
use App\ContentStatus;

it('can bind search strategy interface to MySQL implementation', function () {
    $strategy = app(SearchStrategyInterface::class);
    
    expect($strategy)->toBeInstanceOf(MySqlSearchStrategy::class);
});

it('can search using the bound strategy', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    Story::factory()->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);
    
    $strategy = app(SearchStrategyInterface::class);
    $results = $strategy->search('Test Story');
    
    expect($results)->not->toBeEmpty();
    expect($results->first()['title'])->toBe('Test Story');
});

it('can get result counts using the bound strategy', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    Story::factory()->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);
    
    $strategy = app(SearchStrategyInterface::class);
    $counts = $strategy->getResultCounts('Test Story');
    
    expect($counts)->toHaveKey('stories');
    expect($counts['stories'])->toBe(1);
});
