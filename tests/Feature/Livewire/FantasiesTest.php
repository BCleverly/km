<?php

use App\Livewire\Fantasies\ListFantasies;
use App\Livewire\Fantasies\CreateFantasy;
use App\Models\User;
use App\Models\Fantasy;
use Livewire\Livewire;

it('can list fantasies', function () {
    $user = User::factory()->create();
    
    Fantasy::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => 2, // Approved
    ]);

    Livewire::actingAs($user)
        ->test(ListFantasies::class)
        ->assertSee('Fantasies')
        ->assertSee('Share Fantasy');
});

it('can create a fantasy', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateFantasy::class)
        ->set('content', 'This is a test fantasy with enough words to meet the minimum requirements.')
        ->call('save')
        ->assertRedirect(route('app.fantasies.index'));

    $this->assertDatabaseHas('fantasies', [
        'content' => 'This is a test fantasy with enough words to meet the minimum requirements.',
        'user_id' => $user->id,
        'status' => 1, // Pending
    ]);
});

it('validates fantasy content length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateFantasy::class)
        ->set('content', 'Short')
        ->call('save')
        ->assertHasErrors(['content']);
});

it('can report a fantasy', function () {
    $user = User::factory()->create();
    $fantasy = Fantasy::factory()->create([
        'status' => 2, // Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(ListFantasies::class)
        ->call('reportFantasy', $fantasy->id)
        ->assertDispatched('notify');

    $fantasy->refresh();
    expect($fantasy->report_count)->toBe(1);
});