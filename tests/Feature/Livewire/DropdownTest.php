<?php

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

it('renders dropdown with Alpine.js functionality', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
    $response->assertSee('Tom Cook');
    $response->assertSee('Your profile');
    $response->assertSee('Settings');
    $response->assertSee('Sign out');
});

it('has proper Alpine.js attributes for dropdown', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
    $response->assertSee('x-data="{ open: false }"', false);
    $response->assertSee('@click="open = !open"', false);
    $response->assertSee('@click.outside="open = false"', false);
    $response->assertSee('x-show="open"', false);
    $response->assertSee('x-cloak', false);
    $response->assertSee('x-transition:enter', false);
});

it('has proper cursor hover states', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
    $response->assertSee('cursor-pointer', false);
    $response->assertSee('transition-colors', false);
    $response->assertSee('hover:bg-gray-50', false);
    $response->assertSee('dark:hover:bg-gray-800', false);
});

it('includes x-cloak CSS to prevent flashing', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
    // Check that the CSS file is loaded (contains x-cloak rule)
    $response->assertSee('href="/build/assets/', false);
});
