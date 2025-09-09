<?php

declare(strict_types=1);

use App\Models\User;

it('allows authenticated user to logout', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('logout'));
    
    $response->assertRedirect('/');
    $this->assertGuest();
});

it('redirects unauthenticated user from logout', function () {
    $response = $this->post(route('logout'));
    
    $response->assertRedirect('/login');
});

it('invalidates session on logout', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user);
    
    // Verify user is authenticated
    $this->assertAuthenticated();
    
    // Logout
    $response = $this->post(route('logout'));
    
    $response->assertRedirect('/');
    $this->assertGuest();
});
