<?php

declare(strict_types=1);

use App\Enums\DesireItemType;
use App\Enums\DesireResponseType;
use App\Livewire\DesireDiscovery\DesireDiscovery;
use App\Models\DesireItem;
use App\Models\PartnerDesireResponse;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['user_type' => TargetUserType::Couple]);
    $this->partner = User::factory()->create([
        'user_type' => TargetUserType::Couple,
        'partner_id' => $this->user->id,
    ]);
    $this->user->update(['partner_id' => $this->partner->id]);

    $this->actingAs($this->user);
});

it('requires partner to access desire discovery', function () {
    $userWithoutPartner = User::factory()->create(['user_type' => TargetUserType::Male]);
    $this->actingAs($userWithoutPartner);

    $this->get('/app/desire-discovery/explore')
        ->assertSee('Partner Required');
});

it('shows exploration interface for users with partners', function () {
    $this->get('/app/desire-discovery/explore')
        ->assertSee('Desire Discovery')
        ->assertSee($this->partner->display_name);
});

it('allows users to submit desire items', function () {
    $this->get('/app/desire-discovery/submit')
        ->assertSee('Submit Desire Item');

    Livewire::test(DesireDiscovery::class)
        ->set('activeTab', 'submit')
        ->set('title', 'Test Desire Item')
        ->set('description', 'This is a test description')
        ->set('item_type', DesireItemType::Fantasy->value)
        ->set('target_user_type', TargetUserType::Any->value)
        ->set('difficulty_level', 5)
        ->call('submit')
        ->assertSee('Desire item submitted successfully!');

    $this->assertDatabaseHas('desire_items', [
        'title' => 'Test Desire Item',
        'description' => 'This is a test description',
        'user_id' => $this->user->id,
    ]);
});

it('allows users to respond to desire items', function () {
    $desireItem = DesireItem::factory()->create();

    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'explore');
    $component->set('currentItem', $desireItem);
    $component->call('respond', DesireResponseType::Yes);

    $this->assertDatabaseHas('partner_desire_responses', [
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $desireItem->id,
        'response_type' => DesireResponseType::Yes,
    ]);
});

it('shows compatibility view for users with partners', function () {
    $this->get('/app/desire-discovery/compatibility')
        ->assertSee('Compatibility Report')
        ->assertSee($this->partner->display_name);
});

it('calculates compatibility statistics correctly', function () {
    $desireItem1 = DesireItem::factory()->create();
    $desireItem2 = DesireItem::factory()->create();

    // Both users respond "Yes" to first item (match)
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $desireItem1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->partner->id,
        'partner_id' => $this->user->id,
        'desire_item_id' => $desireItem1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    // Different responses to second item (no match)
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $desireItem2->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->partner->id,
        'partner_id' => $this->user->id,
        'desire_item_id' => $desireItem2->id,
        'response_type' => DesireResponseType::No,
    ]);

    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'compatibility');
    $stats = $component->compatibilityStats;

    expect($stats['both_responded'])->toBe(2);
    expect($stats['matches'])->toBe(1);
    expect($stats['compatibility_percentage'])->toBe(50.0);
    expect($stats['yes_matches'])->toBe(1);
});

it('filters items by type in exploration interface', function () {
    DesireItem::factory()->create(['item_type' => DesireItemType::Fantasy]);
    DesireItem::factory()->create(['item_type' => DesireItemType::Kink]);

    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'explore');

    $component->set('filterType', DesireItemType::Fantasy)
        ->assertSee('Fantasy');
});

it('shows only unresponded items by default', function () {
    $desireItem1 = DesireItem::factory()->create(['target_user_type' => TargetUserType::Couple]);
    $desireItem2 = DesireItem::factory()->create(['target_user_type' => TargetUserType::Couple]);

    // User responds to first item
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $desireItem1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'explore');

    // Force the component to load items
    $component->call('loadItems');

    // Check if items are loaded
    $items = $component->get('items');
    expect($items)->toHaveCount(1);
    expect($items[0]['id'])->toBe($desireItem2->id);
});

it('allows adding and removing tags in submission form', function () {
    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'submit');

    $component->set('newTag', 'test-tag')
        ->call('addTag')
        ->assertSet('tags', ['test-tag'])
        ->assertSet('newTag', '');

    $component->call('removeTag', 'test-tag')
        ->assertSet('tags', []);
});

it('validates required fields in submission form', function () {
    Livewire::test(DesireDiscovery::class)
        ->set('activeTab', 'submit')
        ->call('submit')
        ->assertHasErrors(['title', 'description']);
});

it('prevents duplicate responses from same user', function () {
    $desireItem = DesireItem::factory()->create();

    // First response
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $desireItem->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    // Try to respond again - should update existing response
    $component = Livewire::test(DesireDiscovery::class);
    $component->set('activeTab', 'explore');
    $component->set('currentItem', $desireItem);
    $component->call('respond', DesireResponseType::No);

    $this->assertDatabaseHas('partner_desire_responses', [
        'user_id' => $this->user->id,
        'desire_item_id' => $desireItem->id,
        'response_type' => DesireResponseType::No,
    ]);

    // Should only have one response per user per item
    $this->assertDatabaseCount('partner_desire_responses', 1);
});
