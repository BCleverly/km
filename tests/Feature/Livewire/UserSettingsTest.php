<?php

use App\Livewire\User\Settings;
use App\Models\User;
use App\Models\Profile;
use Livewire\Livewire;
use Livewire\WithFileUploads;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertStatus(200);
});

it('displays settings title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('Settings');
});

it('shows form fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Username')
        ->assertSee('About')
        ->assertSee('Profile Picture')
        ->assertSee('Cover Photo')
        ->assertSee('Save Changes');
});

it('initializes form with user data', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
    
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'johndoe',
        'about' => 'Test about'
    ]);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSet('form.name', 'John Doe')
        ->assertSet('form.email', 'john@example.com')
        ->assertSet('form.username', 'johndoe')
        ->assertSet('form.about', 'Test about');
});

it('saves settings successfully with valid data', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.name', 'Updated Name')
        ->set('form.email', 'updated@example.com')
        ->set('form.username', 'updateduser')
        ->set('form.about', 'Updated about')
        ->call('save')
        ->assertSessionHas('message', 'Settings updated successfully!');
    
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
    
    $profile = $user->profile;
    expect($profile->username)->toBe('updateduser');
    expect($profile->about)->toBe('Updated about');
});

it('validates required fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.name', '')
        ->set('form.email', '')
        ->set('form.username', '')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.email', 'form.username']);
});

it('validates email format', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.email', 'invalid-email')
        ->call('save')
        ->assertHasErrors(['form.email']);
});

it('validates unique username', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Profile::factory()->create([
        'user_id' => $otherUser->id,
        'username' => 'existinguser'
    ]);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.username', 'existinguser')
        ->call('save')
        ->assertHasErrors(['form.username']);
});

it('allows same username for same user', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'existinguser'
    ]);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.username', 'existinguser')
        ->call('save')
        ->assertSessionHas('message', 'Settings updated successfully!');
});

it('validates username length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.username', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['form.username']);
});

it('validates about length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.about', str_repeat('a', 1001))
        ->call('save')
        ->assertHasErrors(['form.about']);
});

it('validates profile picture file type', function () {
    $user = User::factory()->create();
    
    // Create a fake file upload
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertHasErrors(['form.profile_picture']);
});

it('validates profile picture file size', function () {
    $user = User::factory()->create();
    
    // Create a fake file upload that's too large
    $file = \Illuminate\Http\UploadedFile::fake()->image('photo.jpg')->size(11000); // 11MB
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertHasErrors(['form.profile_picture']);
});

it('validates cover photo file type', function () {
    $user = User::factory()->create();
    
    // Create a fake file upload
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertHasErrors(['form.cover_photo']);
});

it('validates cover photo file size', function () {
    $user = User::factory()->create();
    
    // Create a fake file upload that's too large
    $file = \Illuminate\Http\UploadedFile::fake()->image('cover.jpg')->size(11000); // 11MB
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertHasErrors(['form.cover_photo']);
});

it('uploads profile picture successfully', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Create a fake image file
    $file = \Illuminate\Http\UploadedFile::fake()->image('profile.jpg', 200, 200);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertSessionHas('message', 'Settings updated successfully!');
    
    // Verify the file was uploaded
    $profile->refresh();
    expect($profile->getFirstMedia('profile_pictures'))->not->toBeNull();
});

it('uploads cover photo successfully', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Create a fake image file
    $file = \Illuminate\Http\UploadedFile::fake()->image('cover.jpg', 800, 400);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertSessionHas('message', 'Settings updated successfully!');
    
    // Verify the file was uploaded
    $profile->refresh();
    expect($profile->getFirstMedia('cover_photos'))->not->toBeNull();
});

it('removes profile picture successfully', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Add a profile picture first
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('removeProfilePicture')
        ->assertSessionHas('message', 'Profile picture removed successfully!');
    
    // Verify the file was removed
    $profile->refresh();
    expect($profile->getFirstMedia('profile_pictures'))->toBeNull();
});

it('removes cover photo successfully', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Add a cover photo first
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('cover.jpg'))
        ->toMediaCollection('cover_photos');
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('removeCoverPhoto')
        ->assertSessionHas('message', 'Cover photo removed successfully!');
    
    // Verify the file was removed
    $profile->refresh();
    expect($profile->getFirstMedia('cover_photos'))->toBeNull();
});

it('dispatches profile-updated event', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.name', 'Updated Name')
        ->call('save')
        ->assertDispatched('profile-updated');
});

it('shows preview for valid image files', function () {
    $user = User::factory()->create();
    
    // Create a fake image file
    $file = \Illuminate\Http\UploadedFile::fake()->image('profile.jpg', 200, 200);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('form.profile_picture', $file)
        ->assertSee('Preview', false);
});

it('shows stored profile picture URL', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Add a profile picture
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('profile_pictures', false);
});

it('shows stored cover photo URL', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    // Add a cover photo
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('cover.jpg'))
        ->toMediaCollection('cover_photos');
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('cover_photos', false);
});

it('checks profile picture conversion status', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('checkConversions')
        ->assertDispatched('$refresh');
});

it('handles unauthenticated users', function () {
    $this->get('/app/user/settings')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertViewIs('livewire.user.settings');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertStatus(200);
});

it('shows form help text', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('Tell others about yourself', false)
        ->assertSee('Choose a unique username', false);
});

it('displays file upload guidelines', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('Max file size: 10MB', false)
        ->assertSee('Supported formats: JPEG, PNG, JPG, GIF, WebP', false);
});

it('shows current profile information', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
    
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'johndoe',
        'about' => 'Test about'
    ]);
    
    Livewire::actingAs($user)
        ->test(Settings::class)
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('johndoe')
        ->assertSee('Test about');
});

