<?php

use App\Models\PartnerInvitation;
use App\Models\User;
use App\Notifications\PartnerInvitationNotification;
use App\TargetUserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

it('allows couple users to send partner invitations', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    $response = $this->get('/app/settings');

    $response->assertSuccessful();
    $response->assertSee('Invite Your Partner');
});

it('allows lifetime subscribers to send partner invitations', function () {
    $user = User::factory()->create([
        'subscription_plan' => \App\Enums\SubscriptionPlan::Lifetime,
    ]);

    $this->actingAs($user);

    $response = $this->get('/app/settings');

    $response->assertSuccessful();
    $response->assertSee('Invite Your Partner');
});

it('allows admins to send partner invitations', function () {
    $user = User::factory()->create();

    // Create the Admin role if it doesn't exist
    if (! \Spatie\Permission\Models\Role::where('name', 'Admin')->exists()) {
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
    }

    $user->assignRole('Admin');

    $this->actingAs($user);

    $response = $this->get('/app/settings');

    $response->assertSuccessful();
    $response->assertSee('Invite Your Partner');
});

it('denies regular users from sending partner invitations', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Male,
    ]);

    $this->actingAs($user);

    $response = $this->get('/app/settings');

    $response->assertSuccessful();
    $response->assertDontSee('Invite Your Partner');
});

it('can send a partner invitation via livewire component', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner@example.com')
        ->set('invitationMessage', 'Join me on Kink Master!')
        ->call('sendInvitation')
        ->assertHasNoErrors()
        ->assertSet('showInvitationForm', false);

    $this->assertDatabaseHas('partner_invitations', [
        'invited_by' => $user->id,
        'email' => 'partner@example.com',
        'message' => 'Join me on Kink Master!',
    ]);

    Notification::assertSentTo(
        new \Illuminate\Notifications\AnonymousNotifiable,
        PartnerInvitationNotification::class
    );
});

it('validates email format in invitation form', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'invalid-email')
        ->call('sendInvitation')
        ->assertHasErrors(['invitationEmail']);
});

it('prevents sending invitation to existing user', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'existing@example.com')
        ->call('sendInvitation')
        ->assertHasErrors(['invitationEmail']);
});

it('prevents sending multiple invitations (one at a time rule)', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    // Send first invitation
    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner@example.com')
        ->call('sendInvitation')
        ->assertHasNoErrors();

    // Try to send second invitation to different email
    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner2@example.com')
        ->call('sendInvitation')
        ->assertHasErrors(['invitation']);
});

it('prevents duplicate invitations to same email', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    // Send first invitation
    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner@example.com')
        ->call('sendInvitation')
        ->assertHasNoErrors();

    // Try to send second invitation to same email
    Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner@example.com')
        ->call('sendInvitation')
        ->assertHasErrors(['invitationEmail']);
});

it('can accept invitation and create new user', function () {
    $inviter = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $invitation = PartnerInvitation::createInvitation($inviter, 'partner@example.com', 'Join me!');

    $response = $this->post('/partner-invitation/'.$invitation->token, [
        'name' => 'Partner User',
        'email' => 'partner@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('app.dashboard'));

    $this->assertDatabaseHas('users', [
        'name' => 'Partner User',
        'email' => 'partner@example.com',
        'user_type' => TargetUserType::Couple,
        'partner_id' => $inviter->id,
    ]);

    $this->assertDatabaseHas('partner_invitations', [
        'id' => $invitation->id,
        'accepted_at' => now(),
    ]);

    // Check that inviter is linked as partner
    $inviter->refresh();
    $partner = User::where('email', 'partner@example.com')->first();
    expect($inviter->fresh()->partner_id)->toBe($partner->id);
});

it('can accept invitation for existing logged in user', function () {
    $inviter = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $existingUser = User::factory()->create([
        'email' => 'partner@example.com',
        'user_type' => TargetUserType::Male,
    ]);

    $invitation = PartnerInvitation::createInvitation($inviter, 'partner@example.com');

    $this->actingAs($existingUser);

    $response = $this->get('/partner-invitation/'.$invitation->token);

    $response->assertRedirect(route('app.dashboard'));

    $existingUser->refresh();
    $inviter->refresh();

    expect($existingUser->fresh()->partner_id)->toBe($inviter->id);
    expect($inviter->fresh()->partner_id)->toBe($existingUser->id);
});

it('rejects expired invitations', function () {
    $inviter = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $invitation = PartnerInvitation::create([
        'invited_by' => $inviter->id,
        'email' => 'partner@example.com',
        'token' => 'test-token',
        'expires_at' => now()->subDay(),
    ]);

    $response = $this->get('/partner-invitation/'.$invitation->token);

    $response->assertRedirect('/register');
    $response->assertSessionHas('error', 'This invitation has expired.');
});

it('rejects already accepted invitations', function () {
    $inviter = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $invitation = PartnerInvitation::create([
        'invited_by' => $inviter->id,
        'email' => 'partner@example.com',
        'token' => 'test-token',
        'expires_at' => now()->addDay(),
        'accepted_at' => now(),
    ]);

    $response = $this->get('/partner-invitation/'.$invitation->token);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'This invitation has already been accepted.');
});

it('shows sent invitations in the component', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $invitation = PartnerInvitation::createInvitation($user, 'partner@example.com', 'Test message');

    $this->actingAs($user);

    Livewire::test(\App\Livewire\User\Settings::class)
        ->assertSee('partner@example.com')
        ->assertSee('Test message')
        ->assertSee('Pending');
});

it('can send another invitation after success', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(\App\Livewire\User\Settings::class)
        ->set('invitationEmail', 'partner@example.com')
        ->call('sendInvitation')
        ->assertSet('showInvitationForm', false);

    $component->call('sendAnotherInvitation')
        ->assertSet('showInvitationForm', true)
        ->assertSet('invitationEmail', '')
        ->assertSet('invitationMessage', '');
});

it('can clean up expired invitations', function () {
    $user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
    ]);

    // Create an expired invitation
    $expiredInvitation = PartnerInvitation::create([
        'invited_by' => $user->id,
        'email' => 'partner@example.com',
        'token' => 'test-token',
        'expires_at' => now()->subHour(), // Expired 1 hour ago
    ]);

    // Create a valid invitation
    $validInvitation = PartnerInvitation::create([
        'invited_by' => $user->id,
        'email' => 'partner2@example.com',
        'token' => 'test-token-2',
        'expires_at' => now()->addHour(), // Valid for 1 more hour
    ]);

    $this->artisan('invitations:cleanup')
        ->expectsOutput('Cleaned up 1 expired invitations.');

    $this->assertDatabaseMissing('partner_invitations', [
        'id' => $expiredInvitation->id,
    ]);

    $this->assertDatabaseHas('partner_invitations', [
        'id' => $validInvitation->id,
    ]);
});

it('shows no output when no expired invitations exist', function () {
    $this->artisan('invitations:cleanup')
        ->expectsOutput('No expired invitations found.');
});
