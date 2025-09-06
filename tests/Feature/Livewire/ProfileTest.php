<?php

use App\Livewire\User\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders successfully', function () {
    Livewire::test(Profile::class)
        ->assertStatus(200);
});

it('displays the profile form elements', function () {
    Livewire::test(Profile::class)
        ->assertSee('Profile')
        ->assertSee('Username')
        ->assertSee('About')
        ->assertSee('Profile Picture')
        ->assertSee('Cover Photo')
        ->assertSee('Save');
});

it('loads user data into the form', function () {
    $this->user->update([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->user->profile()->update([
        'username' => 'johndoe',
        'about' => 'Test about text',
    ]);

    Livewire::test(Profile::class)
        ->assertSet('form.name', 'John Doe')
        ->assertSet('form.username', 'johndoe')
        ->assertSet('form.email', 'john@example.com')
        ->assertSet('form.about', 'Test about text');
});

it('validates required fields', function () {
    Livewire::test(Profile::class)
        ->set('form.name', '')
        ->set('form.username', '')
        ->set('form.email', '')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.username', 'form.email']);
});

it('validates email format', function () {
    Livewire::test(Profile::class)
        ->set('form.email', 'invalid-email')
        ->call('save')
        ->assertHasErrors(['form.email']);
});

it('validates username uniqueness', function () {
    $otherUser = User::factory()->create();
    $otherUser->profile()->create(['username' => 'existinguser']);

    Livewire::test(Profile::class)
        ->set('form.username', 'existinguser')
        ->call('save')
        ->assertHasErrors(['form.username']);
});

it('allows user to keep their own username', function () {
    $this->user->profile()->create(['username' => 'myusername']);

    Livewire::test(Profile::class)
        ->set('form.username', 'myusername')
        ->call('save')
        ->assertHasNoErrors(['form.username']);
});

it('validates about field length', function () {
    Livewire::test(Profile::class)
        ->set('form.about', str_repeat('a', 1001))
        ->call('save')
        ->assertHasErrors(['form.about']);
});

it('updates user profile successfully', function () {
    // Ensure profile exists
    if (! $this->user->profile) {
        $this->user->profile()->create([
            'username' => 'originaluser',
            'about' => 'Original about text',
        ]);
    }

    Livewire::test(Profile::class)
        ->set('form.name', 'Updated Name')
        ->set('form.username', 'updateduser')
        ->set('form.email', 'updated@example.com')
        ->set('form.about', 'Updated about text')
        ->call('save')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Name');
    expect($this->user->email)->toBe('updated@example.com');

    $profile = $this->user->profile;
    expect($profile)->not->toBeNull();
    expect($profile->username)->toBe('updateduser');
    expect($profile->about)->toBe('Updated about text');
});

it('validates profile picture file type', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 1000);

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertHasErrors(['form.profile_picture']);
});

it('validates profile picture file size', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg')->size(11000); // 11MB

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertHasErrors(['form.profile_picture']);
});

it('uploads profile picture successfully', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Profile updated successfully!');

    $this->user->refresh();
    $profile = $this->user->profile;
    expect($profile)->not->toBeNull();
    expect($profile->getFirstMedia('profile_pictures'))->not->toBeNull();
    expect($profile->getFirstMedia('profile_pictures')->name)->toBe('profile.jpg');
});

it('validates cover photo file type', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 1000);

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertHasErrors(['form.cover_photo']);
});

it('validates cover photo file size', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg')->size(11000); // 11MB

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertHasErrors(['form.cover_photo']);
});

it('uploads cover photo successfully', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSessionHas('message', 'Profile updated successfully!');

    $this->user->refresh();
    $profile = $this->user->profile;
    expect($profile)->not->toBeNull();
    expect($profile->getFirstMedia('cover_photos'))->not->toBeNull();
    expect($profile->getFirstMedia('cover_photos')->name)->toBe('cover.jpg');
});

it('replaces existing profile picture when uploading new one', function () {
    Storage::fake('public');

    // Upload first picture
    $firstFile = UploadedFile::fake()->image('first.jpg', 500, 500);
    Livewire::test(Profile::class)
        ->set('form.profile_picture', $firstFile)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $firstMedia = $profile->getFirstMedia('profile_pictures');
    expect($firstMedia)->not->toBeNull();

    // Upload second picture
    $secondFile = UploadedFile::fake()->image('second.jpg', 500, 500);
    Livewire::test(Profile::class)
        ->set('form.profile_picture', $secondFile)
        ->call('save');

    $this->user->refresh();
    $profile->refresh();
    $secondMedia = $profile->getFirstMedia('profile_pictures');
    expect($secondMedia)->not->toBeNull();
    expect($secondMedia->id)->not->toBe($firstMedia->id);
    expect($secondMedia->name)->toBe('second.jpg');
});

it('replaces existing cover photo when uploading new one', function () {
    Storage::fake('public');

    // Upload first cover photo
    $firstFile = UploadedFile::fake()->image('first-cover.jpg', 1200, 600);
    Livewire::test(Profile::class)
        ->set('form.cover_photo', $firstFile)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $firstMedia = $profile->getFirstMedia('cover_photos');
    expect($firstMedia)->not->toBeNull();

    // Upload second cover photo
    $secondFile = UploadedFile::fake()->image('second-cover.jpg', 1200, 600);
    Livewire::test(Profile::class)
        ->set('form.cover_photo', $secondFile)
        ->call('save');

    $this->user->refresh();
    $profile->refresh();
    $secondMedia = $profile->getFirstMedia('cover_photos');
    expect($secondMedia)->not->toBeNull();
    expect($secondMedia->id)->not->toBe($firstMedia->id);
    expect($secondMedia->name)->toBe('second-cover.jpg');
});

it('removes profile picture successfully', function () {
    Storage::fake('public');

    // Upload a profile picture first
    $file = UploadedFile::fake()->image('profile.jpg', 500, 500);
    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    expect($profile->getFirstMedia('profile_pictures'))->not->toBeNull();

    // Remove the profile picture
    Livewire::test(Profile::class)
        ->call('removeProfilePicture')
        ->assertSessionHas('message', 'Profile picture removed successfully!');

    $this->user->refresh();
    $profile->refresh();
    expect($profile->getFirstMedia('profile_pictures'))->toBeNull();
});

it('removes cover photo successfully', function () {
    Storage::fake('public');

    // Upload a cover photo first
    $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);
    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    expect($profile->getFirstMedia('cover_photos'))->not->toBeNull();

    // Remove the cover photo
    Livewire::test(Profile::class)
        ->call('removeCoverPhoto')
        ->assertSessionHas('message', 'Cover photo removed successfully!');

    $this->user->refresh();
    $profile->refresh();
    expect($profile->getFirstMedia('cover_photos'))->toBeNull();
});

it('generates media conversions for profile pictures', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $media = $profile->getFirstMedia('profile_pictures');

    expect($media)->not->toBeNull();
    expect($media->hasGeneratedConversion('profile_thumb'))->toBeTrue();
    expect($media->hasGeneratedConversion('profile_medium'))->toBeTrue();
});

it('generates media conversions for cover photos', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $media = $profile->getFirstMedia('cover_photos');

    expect($media)->not->toBeNull();
    expect($media->hasGeneratedConversion('cover_thumb'))->toBeTrue();
    expect($media->hasGeneratedConversion('cover_medium'))->toBeTrue();
});

it('shows gravatar when no profile picture is uploaded', function () {
    Livewire::test(Profile::class)
        ->assertSee('gravatar.com');
});

it('displays profile picture when uploaded', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $media = $profile->getFirstMedia('profile_pictures');

    Livewire::test(Profile::class)
        ->assertSee($media->getUrl('profile_thumb'));
});

it('displays cover photo when uploaded', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save');

    $this->user->refresh();
    $profile = $this->user->profile;
    $media = $profile->getFirstMedia('cover_photos');

    Livewire::test(Profile::class)
        ->assertSee($media->getUrl('cover_medium'));
});

it('shows remove button when profile picture exists', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

    Livewire::test(Profile::class)
        ->set('form.profile_picture', $file)
        ->call('save');

    $this->user->refresh();

    Livewire::test(Profile::class)
        ->assertSee('Remove');
});

it('shows remove button when cover photo exists', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

    Livewire::test(Profile::class)
        ->set('form.cover_photo', $file)
        ->call('save');

    $this->user->refresh();

    Livewire::test(Profile::class)
        ->assertSee('Remove');
});

it('accepts only image files for profile picture', function () {
    Storage::fake('public');

    $validFiles = [
        UploadedFile::fake()->image('profile.jpg'),
        UploadedFile::fake()->image('profile.png'),
        UploadedFile::fake()->image('profile.gif'),
        UploadedFile::fake()->image('profile.webp'),
    ];

    foreach ($validFiles as $file) {
        Livewire::test(Profile::class)
            ->set('form.profile_picture', $file)
            ->call('save')
            ->assertHasNoErrors(['form.profile_picture']);
    }
});

it('accepts only image files for cover photo', function () {
    Storage::fake('public');

    $validFiles = [
        UploadedFile::fake()->image('cover.jpg'),
        UploadedFile::fake()->image('cover.png'),
        UploadedFile::fake()->image('cover.gif'),
        UploadedFile::fake()->image('cover.webp'),
    ];

    foreach ($validFiles as $file) {
        Livewire::test(Profile::class)
            ->set('form.cover_photo', $file)
            ->call('save')
            ->assertHasNoErrors(['form.cover_photo']);
    }
});

it('allows user to update theme preference', function () {
    // Ensure user has a profile
    if (! $this->user->profile) {
        $this->user->profile()->create([
            'username' => 'testuser',
            'about' => 'Test user',
            'theme_preference' => 'system',
        ]);
    }

    Livewire::test(Profile::class)
        ->set('form.theme_preference', 'dark')
        ->call('save')
        ->assertHasNoErrors(['form.theme_preference']);

    expect($this->user->fresh()->profile->theme_preference)->toBe('dark');
});

it('validates theme preference values', function () {
    Livewire::test(Profile::class)
        ->set('form.theme_preference', 'invalid')
        ->call('save')
        ->assertHasErrors(['form.theme_preference']);
});

it('displays theme preference options', function () {
    Livewire::test(Profile::class)
        ->assertSee('Account Preferences')
        ->assertSee('Theme Preference')
        ->assertSee('Light')
        ->assertSee('Dark')
        ->assertSee('System (Follow device preference)');
});
