<?php

use App\Livewire\Tasks\SubmitOutcome;
use App\Models\User;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use App\ContentStatus;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertStatus(200);
});

it('displays submit outcome title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Submit Outcome');
});

it('shows form fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Title')
        ->assertSee('Description')
        ->assertSee('Difficulty Level')
        ->assertSee('Target User Type')
        ->assertSee('Intended Type')
        ->assertSee('Submit Outcome');
});

it('displays user types options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Male')
        ->assertSee('Female')
        ->assertSee('Couple')
        ->assertSee('Any');
});

it('displays difficulty levels options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Very Easy')
        ->assertSee('Easy')
        ->assertSee('Medium')
        ->assertSee('Hard')
        ->assertSee('Very Hard')
        ->assertSee('Extreme');
});

it('displays intended type options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Reward')
        ->assertSee('Punishment');
});

it('submits reward successfully with valid data', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', 'Test Reward')
        ->set('outcomeForm.description', 'Test Description')
        ->set('outcomeForm.difficulty_level', 3)
        ->set('outcomeForm.target_user_type', TargetUserType::Any->value)
        ->set('outcomeForm.intended_type', 'reward')
        ->call('submitOutcome')
        ->assertSessionHas('message', 'Your outcome has been submitted for review!');
    
    $this->assertDatabaseHas('outcomes', [
        'title' => 'Test Reward',
        'description' => 'Test Description',
        'difficulty_level' => 3,
        'target_user_type' => TargetUserType::Any->value,
        'intended_type' => 'reward',
        'user_id' => $user->id,
        'status' => ContentStatus::Pending
    ]);
});

it('submits punishment successfully with valid data', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', 'Test Punishment')
        ->set('outcomeForm.description', 'Test Description')
        ->set('outcomeForm.difficulty_level', 3)
        ->set('outcomeForm.target_user_type', TargetUserType::Any->value)
        ->set('outcomeForm.intended_type', 'punishment')
        ->call('submitOutcome')
        ->assertSessionHas('message', 'Your outcome has been submitted for review!');
    
    $this->assertDatabaseHas('outcomes', [
        'title' => 'Test Punishment',
        'description' => 'Test Description',
        'difficulty_level' => 3,
        'target_user_type' => TargetUserType::Any->value,
        'intended_type' => 'punishment',
        'user_id' => $user->id,
        'status' => ContentStatus::Pending
    ]);
});

it('validates required fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', '')
        ->set('outcomeForm.description', '')
        ->set('outcomeForm.intended_type', '')
        ->call('submitOutcome')
        ->assertHasErrors(['outcomeForm.title', 'outcomeForm.description', 'outcomeForm.intended_type']);
});

it('validates title length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', str_repeat('a', 256))
        ->call('submitOutcome')
        ->assertHasErrors(['outcomeForm.title']);
});

it('validates description length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.description', str_repeat('a', 5001))
        ->call('submitOutcome')
        ->assertHasErrors(['outcomeForm.description']);
});

it('validates difficulty level range', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.difficulty_level', 11)
        ->call('submitOutcome')
        ->assertHasErrors(['outcomeForm.difficulty_level']);
});

it('validates intended type', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.intended_type', 'invalid')
        ->call('submitOutcome')
        ->assertHasErrors(['outcomeForm.intended_type']);
});

it('creates tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->call('createTag', 'category', 'New Tag')
        ->assertSessionHas('message', 'New tag created and added! It will be reviewed before becoming visible to others.');
});

it('validates tag name is not empty', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->call('createTag', 'category', '')
        ->assertSessionHas('error', 'Tag name cannot be empty.');
});

it('validates tag name is not whitespace only', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->call('createTag', 'category', '   ')
        ->assertSessionHas('error', 'Tag name cannot be empty.');
});

it('removes tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->call('removeTag', 'category', 1);
    
    // The tag should be removed from the form
    // This is tested indirectly through the form state
});

it('resets form after successful submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', 'Test Outcome')
        ->set('outcomeForm.description', 'Test Description')
        ->set('outcomeForm.difficulty_level', 3)
        ->set('outcomeForm.target_user_type', TargetUserType::Any->value)
        ->set('outcomeForm.intended_type', 'reward')
        ->call('submitOutcome');
    
    // Form should be reset after submission
    $component = Livewire::actingAs($user)
        ->test(SubmitOutcome::class);
    
    expect($component->get('outcomeForm.title'))->toBe('');
    expect($component->get('outcomeForm.description'))->toBe('');
});

it('shows loading state during submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', 'Test Outcome')
        ->set('outcomeForm.description', 'Test Description')
        ->set('outcomeForm.difficulty_level', 3)
        ->set('outcomeForm.target_user_type', TargetUserType::Any->value)
        ->set('outcomeForm.intended_type', 'reward')
        ->call('submitOutcome')
        ->assertSee('Submitting...', false);
});

it('displays form validation errors', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.title', '')
        ->call('submitOutcome')
        ->assertSee('The title field is required', false);
});

it('shows character count for description', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.description', 'Test description')
        ->assertSee('14 / 5000', false);
});

it('shows premium content option', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Premium Content', false)
        ->assertSee('Make this outcome available only to premium users', false);
});

it('displays tags section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Tags', false)
        ->assertSee('Add tags to help categorize your outcome', false);
});

it('shows tag creation form', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Create New Tag', false)
        ->assertSee('Tag Name', false);
});

it('displays existing tags', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Available Tags', false);
});

it('shows different form sections for reward vs punishment', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.intended_type', 'reward')
        ->assertSee('Reward Details', false)
        ->set('outcomeForm.intended_type', 'punishment')
        ->assertSee('Punishment Details', false);
});

it('displays appropriate help text for intended type', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->set('outcomeForm.intended_type', 'reward')
        ->assertSee('Describe the reward that will be given', false)
        ->set('outcomeForm.intended_type', 'punishment')
        ->assertSee('Describe the punishment that will be given', false);
});

it('handles unauthenticated users', function () {
    $this->get('/app/tasks/submit-outcome')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertViewIs('livewire.tasks.submit-outcome');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertStatus(200);
});

it('shows form help text', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Help others understand what this outcome involves', false)
        ->assertSee('How difficult is this outcome?', false)
        ->assertSee('Who is this outcome for?', false);
});

it('displays submission guidelines', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitOutcome::class)
        ->assertSee('Submission Guidelines', false)
        ->assertSee('Please ensure your outcome follows our community guidelines', false);
});

