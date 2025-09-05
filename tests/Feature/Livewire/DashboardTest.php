<?php

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertStatus(200);
});

it('displays hello world message', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Hello World!')
        ->assertSee('Welcome to your Kink Master dashboard');
});

it('uses the app layout', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
    $response->assertSee('Dashboard - Kink Master', false); // Check for title in layout
});

it('has proper styling classes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('min-h-screen bg-gray-50 dark:bg-gray-900', false)
        ->assertSee('bg-white dark:bg-gray-800', false)
        ->assertSee('rounded-xl shadow-sm', false);
});

it('redirects unauthenticated users to login', function () {
    $this->get('/app/dashboard')
        ->assertRedirect('/login');
});
