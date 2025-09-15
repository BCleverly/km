<?php

use App\Livewire\User\PublicProfile;
use App\Models\User;
use App\Models\Profile;
use App\Models\Tasks\TaskActivity;
use App\Models\UserOutcome;
use Livewire\Livewire;

it('renders successfully with valid username', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertStatus(200);
});

it('displays user information', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'created_at' => now()->subMonths(2)
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'johndoe',
        'about' => 'Test about'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'johndoe'])
        ->assertSee('John Doe')
        ->assertSee('johndoe')
        ->assertSee('Test about');
});

it('shows user statistics', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Create some task activities
    TaskActivity::factory()->count(5)->create(['user_id' => $user->id]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Statistics', false)
        ->assertSee('Completed Tasks', false)
        ->assertSee('Current Streak', false)
        ->assertSee('Total Points', false);
});

it('displays recent activities', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Create some task activities
    TaskActivity::factory()->count(3)->create(['user_id' => $user->id]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Recent Activities', false);
});

it('shows profile picture', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Add a profile picture
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('profile_pictures', false);
});

it('shows cover photo', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Add a cover photo
    $profile->addMedia(\Illuminate\Http\UploadedFile::fake()->image('cover.jpg'))
        ->toMediaCollection('cover_photos');
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('cover_photos', false);
});

it('falls back to gravatar when no profile picture', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com'
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('gravatar', false);
});

it('shows joined date', function () {
    $user = User::factory()->create([
        'created_at' => now()->subMonths(3)
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Joined', false)
        ->assertSee(now()->subMonths(3)->format('F Y'));
});

it('displays display name or falls back to name', function () {
    $user = User::factory()->create([
        'name' => 'John Doe'
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('John Doe');
});

it('shows about section when available', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser',
        'about' => 'This is my about section'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('This is my about section');
});

it('hides about section when empty', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser',
        'about' => null
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertDontSee('About', false);
});

it('shows completed tasks count', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Create some completed task activities
    TaskActivity::factory()->count(10)->create([
        'user_id' => $user->id,
        'activity_type' => 'completed'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('10', false);
});

it('shows current streak', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Streak', false);
});

it('shows total points', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Points', false);
});

it('displays recent activities with limit', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    // Create more activities than the default limit
    TaskActivity::factory()->count(10)->create(['user_id' => $user->id]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Recent Activities', false);
});

it('shows achievement badges', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Achievements', false)
        ->assertSee('Badges', false);
});

it('displays user level or rank', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Level', false);
});

it('shows favorite categories', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Favorite Categories', false);
});

it('displays social links if available', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Social Links', false);
});

it('shows contact information if public', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Contact', false);
});

it('displays profile completion percentage', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Profile Completion', false);
});

it('shows member since date', function () {
    $user = User::factory()->create([
        'created_at' => now()->subYears(1)
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Member since', false)
        ->assertSee(now()->subYears(1)->format('F Y'));
});

it('displays last active date', function () {
    $user = User::factory()->create([
        'updated_at' => now()->subDays(2)
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Last active', false);
});

it('shows profile views count', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Profile Views', false);
});

it('displays follow/friend buttons for authenticated users', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    $viewer = User::factory()->create();
    
    Livewire::actingAs($viewer)
        ->test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Follow', false)
        ->assertSee('Message', false);
});

it('handles non-existent username', function () {
    $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
    
    Livewire::test(PublicProfile::class, ['username' => 'nonexistent'])
        ->assertStatus(404);
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertViewIs('livewire.user.public-profile');
});

it('displays proper page title', function () {
    $user = User::factory()->create([
        'name' => 'John Doe'
    ]);
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertStatus(200);
});

it('shows profile header with cover photo', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Profile Header', false);
});

it('displays profile navigation tabs', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create([
        'user_id' => $user->id,
        'username' => 'testuser'
    ]);
    
    Livewire::test(PublicProfile::class, ['username' => 'testuser'])
        ->assertSee('Overview', false)
        ->assertSee('Activities', false)
        ->assertSee('Achievements', false);
});

