<?php

declare(strict_types=1);

use App\ContentStatus;
use App\Enums\DesireItemType;
use App\Models\DesireCategory;
use App\Models\DesireItem;
use App\Models\PartnerDesireResponse;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates desire item with correct attributes', function () {
    $user = User::factory()->create();
    $category = DesireCategory::factory()->create(['item_type' => DesireItemType::Fantasy]);

    $desireItem = DesireItem::create([
        'title' => 'Test Fantasy',
        'description' => 'A test fantasy description',
        'item_type' => DesireItemType::Fantasy,
        'category_id' => $category->id,
        'target_user_type' => TargetUserType::Any,
        'user_id' => $user->id,
        'status' => ContentStatus::Pending,
        'difficulty_level' => 5,
        'tags' => ['test', 'fantasy'],
    ]);

    expect($desireItem->title)->toBe('Test Fantasy');
    expect($desireItem->item_type)->toBe(DesireItemType::Fantasy);
    expect($desireItem->target_user_type)->toBe(TargetUserType::Any);
    expect($desireItem->status)->toBe(ContentStatus::Pending);
    expect($desireItem->tags)->toBe(['test', 'fantasy']);
});

it('belongs to author', function () {
    $user = User::factory()->create();
    $desireItem = DesireItem::factory()->create(['user_id' => $user->id]);

    expect($desireItem->author)->toBeInstanceOf(User::class);
    expect($desireItem->author->id)->toBe($user->id);
});

it('belongs to category', function () {
    $category = DesireCategory::factory()->create();
    $desireItem = DesireItem::factory()->create(['category_id' => $category->id]);

    expect($desireItem->category)->toBeInstanceOf(DesireCategory::class);
    expect($desireItem->category->id)->toBe($category->id);
});

it('has many partner responses', function () {
    $desireItem = DesireItem::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    PartnerDesireResponse::factory()->create([
        'desire_item_id' => $desireItem->id,
        'user_id' => $user1->id,
    ]);

    PartnerDesireResponse::factory()->create([
        'desire_item_id' => $desireItem->id,
        'user_id' => $user2->id,
    ]);

    expect($desireItem->partnerResponses)->toHaveCount(2);
});

it('scopes approved items correctly', function () {
    DesireItem::factory()->create(['status' => ContentStatus::Approved]);
    DesireItem::factory()->create(['status' => ContentStatus::Pending]);
    DesireItem::factory()->create(['status' => ContentStatus::Rejected]);

    $approvedItems = DesireItem::approved()->get();

    expect($approvedItems)->toHaveCount(1);
    expect($approvedItems->first()->status)->toBe(ContentStatus::Approved);
});

it('scopes items for user type correctly', function () {
    DesireItem::factory()->create(['target_user_type' => TargetUserType::Male]);
    DesireItem::factory()->create(['target_user_type' => TargetUserType::Female]);
    DesireItem::factory()->create(['target_user_type' => TargetUserType::Any]);

    $maleItems = DesireItem::forUserType(TargetUserType::Male)->get();
    $anyItems = DesireItem::forUserType(TargetUserType::Any)->get();

    expect($maleItems)->toHaveCount(2); // Male + Any
    expect($anyItems)->toHaveCount(1); // Only Any
});

it('scopes items by item type correctly', function () {
    DesireItem::factory()->create(['item_type' => DesireItemType::Fantasy]);
    DesireItem::factory()->create(['item_type' => DesireItemType::Kink]);
    DesireItem::factory()->create(['item_type' => DesireItemType::Toy]);

    $fantasyItems = DesireItem::forItemType(DesireItemType::Fantasy)->get();

    expect($fantasyItems)->toHaveCount(1);
    expect($fantasyItems->first()->item_type)->toBe(DesireItemType::Fantasy);
});

it('scopes items not responded by user correctly', function () {
    $user = User::factory()->create();
    $desireItem1 = DesireItem::factory()->create();
    $desireItem2 = DesireItem::factory()->create();

    PartnerDesireResponse::factory()->create([
        'user_id' => $user->id,
        'desire_item_id' => $desireItem1->id,
    ]);

    $unrespondedItems = DesireItem::notRespondedBy($user->id)->get();

    expect($unrespondedItems)->toHaveCount(1);
    expect($unrespondedItems->first()->id)->toBe($desireItem2->id);
});

it('scopes items responded by user correctly', function () {
    $user = User::factory()->create();
    $desireItem1 = DesireItem::factory()->create();
    $desireItem2 = DesireItem::factory()->create();

    PartnerDesireResponse::factory()->create([
        'user_id' => $user->id,
        'desire_item_id' => $desireItem1->id,
    ]);

    $respondedItems = DesireItem::respondedBy($user->id)->get();

    expect($respondedItems)->toHaveCount(1);
    expect($respondedItems->first()->id)->toBe($desireItem1->id);
});

it('scopes premium items correctly', function () {
    DesireItem::factory()->create(['is_premium' => true]);
    DesireItem::factory()->create(['is_premium' => false]);
    DesireItem::factory()->create(['is_premium' => true]);

    $premiumItems = DesireItem::premium()->get();

    expect($premiumItems)->toHaveCount(2);
    expect($premiumItems->every(fn ($item) => $item->is_premium))->toBeTrue();
});

it('scopes items by difficulty range correctly', function () {
    DesireItem::factory()->create(['difficulty_level' => 2]);
    DesireItem::factory()->create(['difficulty_level' => 5]);
    DesireItem::factory()->create(['difficulty_level' => 8]);

    $mediumItems = DesireItem::byDifficulty(3, 7)->get();

    expect($mediumItems)->toHaveCount(1);
    expect($mediumItems->first()->difficulty_level)->toBe(5);
});
