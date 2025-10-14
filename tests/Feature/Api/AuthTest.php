<?php

declare(strict_types=1);

use App\Models\User;
use App\TargetUserType;
use App\Enums\SubscriptionPlan;

it('can register a new user', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'user_type' => TargetUserType::Male->value,
        'username' => 'johndoe',
        'bdsm_role' => 1,
        'about' => 'About me...',
    ];

    $response = $this->postJson('/api/v1/auth/register', $userData);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'user_type',
                'subscription_plan',
                'profile' => [
                    'username',
                    'about',
                    'bdsm_role',
                ],
            ],
            'token',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('user.name'))->toBe('John Doe');
    expect($response->json('user.email'))->toBe('john@example.com');
    expect($response->json('token'))->not->toBeNull();

    // Verify user was created in database
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'user_type' => TargetUserType::Male->value,
        'subscription_plan' => SubscriptionPlan::Free->value,
    ]);

    // Verify profile was created
    $this->assertDatabaseHas('profiles', [
        'username' => 'johndoe',
        'about' => 'About me...',
        'bdsm_role' => 1,
    ]);
});

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'user',
            'token',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('user.email'))->toBe('john@example.com');
    expect($response->json('token'))->not->toBeNull();
});

it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
});

it('can get authenticated user profile', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'johndoe',
        'about' => 'About me...',
        'bdsm_role' => 1,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/user');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'user' => [
                'id',
                'name',
                'email',
                'user_type',
                'subscription_plan',
                'profile' => [
                    'username',
                    'about',
                    'bdsm_role',
                ],
                'permissions',
                'limits',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('user.id'))->toBe($user->id);
    expect($response->json('user.profile.username'))->toBe('johndoe');
});

it('can logout and revoke token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
        ->postJson('/api/v1/auth/logout');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);

    // Verify token was revoked
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);
});

it('validates registration data', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
        'user_type' => 999,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => [
                'name',
                'email',
                'password',
                'user_type',
            ],
        ]);
});

it('prevents duplicate email registration', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'user_type' => TargetUserType::Male->value,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['email'],
        ]);
});