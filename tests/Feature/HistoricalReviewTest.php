<?php

use App\Enums\DesireItemType;
use App\Enums\DesireResponseType;
use App\Livewire\DesireDiscovery\HistoricalReview;
use App\Models\DesireItem;
use App\Models\PartnerDesireResponse;
use App\Models\User;
use App\TargetUserType;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['user_type' => TargetUserType::Couple]);
    $this->partner = User::factory()->create(['user_type' => TargetUserType::Couple]);
    $this->user->update(['partner_id' => $this->partner->id]);
    $this->partner->update(['partner_id' => $this->user->id]);

    // Refresh the user to ensure partner_id is loaded
    $this->user = $this->user->fresh();
    $this->partner = $this->partner->fresh();

    $this->actingAs($this->user);
});

it('requires partner or admin to access historical review', function () {
    $userWithoutPartner = User::factory()->create(['partner_id' => null]);
    $this->actingAs($userWithoutPartner);

    Livewire::test(HistoricalReview::class)
        ->assertSee('Access Required');
});

it('shows historical review for users with partners', function () {
    Livewire::test(HistoricalReview::class)
        ->assertSee('Historical Review')
        ->assertSee('Review your past responses');
});

it('shows historical review for admin users', function () {
    // Create Admin role if it doesn't exist
    if (! \Spatie\Permission\Models\Role::where('name', 'Admin')->exists()) {
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
    }

    $admin = User::factory()->create(['partner_id' => null]);
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    Livewire::test(HistoricalReview::class)
        ->assertSee('Historical Review')
        ->assertSee('Admin view of all historical responses');
});

it('displays response statistics correctly', function () {
    // Create some desire items and responses
    $item1 = DesireItem::factory()->create(['target_user_type' => TargetUserType::Couple]);
    $item2 = DesireItem::factory()->create(['target_user_type' => TargetUserType::Couple]);

    // User responds to both items
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item2->id,
        'response_type' => DesireResponseType::No,
    ]);

    // Partner responds to one item
    PartnerDesireResponse::create([
        'user_id' => $this->partner->id,
        'partner_id' => $this->user->id,
        'desire_item_id' => $item1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    $component = Livewire::test(HistoricalReview::class);

    expect($component->get('responseStats')['user_responses'])->toBe(2);
    expect($component->get('responseStats')['partner_responses'])->toBe(1);
});

it('filters items by type', function () {
    $fetishItem = DesireItem::factory()->create([
        'item_type' => DesireItemType::Fetish,
        'target_user_type' => TargetUserType::Couple,
    ]);

    $fantasyItem = DesireItem::factory()->create([
        'item_type' => DesireItemType::Fantasy,
        'target_user_type' => TargetUserType::Couple,
    ]);

    // User responds to both items
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $fetishItem->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $fantasyItem->id,
        'response_type' => DesireResponseType::No,
    ]);

    $component = Livewire::test(HistoricalReview::class);

    // Should show both items initially
    expect($component->get('historicalItems')->count())->toBe(2);

    // Filter by fetish type
    $component->set('filterType', DesireItemType::Fetish);

    expect($component->get('historicalItems')->count())->toBe(1);
    expect($component->get('historicalItems')->first()->id)->toBe($fetishItem->id);
});

it('searches items by title and description', function () {
    $item1 = DesireItem::factory()->create([
        'title' => 'Role Play Scenarios',
        'description' => 'Exploring different role play scenarios',
        'target_user_type' => TargetUserType::Couple,
    ]);

    $item2 = DesireItem::factory()->create([
        'title' => 'Sensory Play',
        'description' => 'Using blindfolds and feathers',
        'target_user_type' => TargetUserType::Couple,
    ]);

    // User responds to both items
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item2->id,
        'response_type' => DesireResponseType::No,
    ]);

    $component = Livewire::test(HistoricalReview::class);

    // Should show both items initially
    expect($component->get('historicalItems')->count())->toBe(2);

    // Search by title
    $component->set('search', 'Role Play');

    expect($component->get('historicalItems')->count())->toBe(1);
    expect($component->get('historicalItems')->first()->id)->toBe($item1->id);

    // Search by description
    $component->set('search', 'blindfolds');

    expect($component->get('historicalItems')->count())->toBe(1);
    expect($component->get('historicalItems')->first()->id)->toBe($item2->id);
});

it('sorts items correctly', function () {
    $item1 = DesireItem::factory()->create([
        'title' => 'Alpha Item',
        'target_user_type' => TargetUserType::Couple,
    ]);

    $item2 = DesireItem::factory()->create([
        'title' => 'Beta Item',
        'target_user_type' => TargetUserType::Couple,
    ]);

    // User responds to both items
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item1->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item2->id,
        'response_type' => DesireResponseType::No,
    ]);

    $component = Livewire::test(HistoricalReview::class);

    // Sort by title ascending
    $component->call('sortBy', 'title');

    $items = $component->get('historicalItems');
    expect($items->first()->title)->toBe('Alpha Item');

    // Sort by title descending
    $component->call('sortBy', 'title');

    $items = $component->get('historicalItems');
    expect($items->first()->title)->toBe('Beta Item');
});

it('shows match indicators for compatible responses', function () {
    $item = DesireItem::factory()->create(['target_user_type' => TargetUserType::Couple]);

    // Both user and partner respond with Yes
    PartnerDesireResponse::create([
        'user_id' => $this->user->id,
        'partner_id' => $this->partner->id,
        'desire_item_id' => $item->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    PartnerDesireResponse::create([
        'user_id' => $this->partner->id,
        'partner_id' => $this->user->id,
        'desire_item_id' => $item->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    Livewire::test(HistoricalReview::class)
        ->assertSee('Perfect Match!');
});

it('shows empty state when no responses exist', function () {
    // Ensure no desire items exist that could be returned by the query
    DesireItem::whereIn('target_user_type', [TargetUserType::Couple, TargetUserType::Any])->delete();

    // Also ensure no partner responses exist
    PartnerDesireResponse::where('user_id', $this->user->id)->delete();
    if ($this->partner) {
        PartnerDesireResponse::where('user_id', $this->partner->id)->delete();
    }

    $component = Livewire::test(HistoricalReview::class);

    // Check that the historical items collection is empty
    $historicalItems = $component->get('historicalItems');
    expect($historicalItems->count())->toBe(0);

    $component->assertSee('No Historical Data')
        ->assertSee('You haven\'t responded to any desire items yet')
        ->assertSee('Start Exploring');
});

it('handles admin users without partners gracefully', function () {
    // Create Admin role if it doesn't exist
    if (! \Spatie\Permission\Models\Role::where('name', 'Admin')->exists()) {
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
    }

    $admin = User::factory()->create(['partner_id' => null]);
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    $item = DesireItem::factory()->create(['target_user_type' => TargetUserType::Any]);

    // Admin responds to item
    PartnerDesireResponse::create([
        'user_id' => $admin->id,
        'partner_id' => null,
        'desire_item_id' => $item->id,
        'response_type' => DesireResponseType::Yes,
    ]);

    $component = Livewire::test(HistoricalReview::class);

    expect($component->get('responseStats')['user_responses'])->toBe(1);
    expect($component->get('responseStats')['partner_responses'])->toBe(0);
    expect($component->get('historicalItems')->count())->toBe(1);
});
