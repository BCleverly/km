<?php

use App\Models\User;

it('redirects unauthenticated users to login', function () {
    $response = $this->get('/app/dashboard');
    
    $response->assertRedirect('/login');
});

it('allows authenticated users to access dashboard', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/dashboard');
    
    $response->assertStatus(200);
});

it('allows authenticated users to access profile', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/profile');
    
    $response->assertStatus(200);
});

it('allows authenticated users to access tasks', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/tasks');
    
    $response->assertStatus(200);
});

it('allows authenticated users to access submit page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/submit');
    
    $response->assertStatus(200);
});

it('allows authenticated users to access stories', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/stories');
    
    $response->assertStatus(200);
});

it('allows authenticated users to access settings', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/settings');
    
    $response->assertStatus(200);
});

it('redirects unauthenticated users from all app routes', function () {
    $appRoutes = [
        '/app/dashboard',
        '/app/profile',
        '/app/tasks',
        '/app/submit',
        '/app/stories',
        '/app/settings',
    ];
    
    foreach ($appRoutes as $route) {
        $response = $this->get($route);
        $response->assertRedirect('/login');
    }
});

it('uses correct route names', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user);
    
    expect(route('app.dashboard'))->toBe('http://localhost/app/dashboard');
    expect(route('app.profile'))->toBe('http://localhost/app/profile');
    expect(route('app.tasks'))->toBe('http://localhost/app/tasks');
    expect(route('app.submit'))->toBe('http://localhost/app/submit');
    expect(route('app.stories'))->toBe('http://localhost/app/stories');
    expect(route('app.settings'))->toBe('http://localhost/app/settings');
});
