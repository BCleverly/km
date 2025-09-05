<?php

use App\Livewire\User\Profile;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertStatus(200);
});

it('displays profile form with user data', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'about' => 'Test user description'
    ]);
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('johndoe')
        ->assertSee('Test user description');
});

it('validates required fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('form.name', '')
        ->set('form.email', '')
        ->set('form.username', '')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.email', 'form.username']);
});

it('validates email format', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('form.email', 'invalid-email')
        ->call('save')
        ->assertHasErrors(['form.email']);
});

it('validates username uniqueness', function () {
    $user1 = User::factory()->create(['username' => 'existinguser']);
    $user2 = User::factory()->create();
    
    Livewire::actingAs($user2)
        ->test(Profile::class)
        ->set('form.username', 'existinguser')
        ->call('save')
        ->assertHasErrors(['form.username']);
});

it('allows user to update their own username', function () {
    $user = User::factory()->create(['username' => 'oldusername']);
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('form.username', 'newusername')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profile updated successfully!');
});

it('updates user profile successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('form.name', 'Updated Name')
        ->set('form.email', 'updated@example.com')
        ->set('form.username', 'updateduser')
        ->set('form.about', 'Updated about text')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profile updated successfully!');
    
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
    expect($user->username)->toBe('updateduser');
    expect($user->about)->toBe('Updated about text');
});

it('redirects unauthenticated users to login', function () {
    $this->get('/app/profile')
        ->assertRedirect('/login');
});

it('has proper styling and layout', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/app/profile');
    
    $response->assertStatus(200);
    $response->assertSee('Profile - Kink Master', false);
    $response->assertSee('This information will be displayed publicly', false);
    $response->assertSee('kink-master.com/', false);
    $response->assertSee('Upload a file', false);
    $response->assertSee('PNG, JPG, GIF up to 10MB', false);
});
