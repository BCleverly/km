<?php

use App\Models\User;

it('allows guest users to access login page', function () {
    $response = $this->get('/login');
    
    $response->assertStatus(200);
    $response->assertSeeLivewire('auth.login');
});

it('allows guest users to access register page', function () {
    $response = $this->get('/register');
    
    $response->assertStatus(200);
    $response->assertSeeLivewire('auth.register');
});

it('allows guest users to access forgot password page', function () {
    $response = $this->get('/forgot-password');
    
    $response->assertStatus(200);
    $response->assertSeeLivewire('auth.forgot-password');
});

it('allows guest users to access reset password page', function () {
    $response = $this->get('/reset-password/test-token');
    
    $response->assertStatus(200);
    $response->assertSeeLivewire('auth.reset-password');
});

it('redirects authenticated users away from login page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/login');
    
    $response->assertRedirect('/');
});

it('redirects authenticated users away from register page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/register');
    
    $response->assertRedirect('/');
});

it('redirects authenticated users away from forgot password page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/forgot-password');
    
    $response->assertRedirect('/');
});

it('redirects authenticated users away from reset password page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/reset-password/test-token');
    
    $response->assertRedirect('/');
});
