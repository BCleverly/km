<?php

use App\Livewire\Tasks\BrowseTasks;
use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use App\ContentStatus;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertStatus(200);
});

it('displays browse tasks title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('Browse Tasks');
});

it('shows tasks by default', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['status' => ContentStatus::Approved]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee($task->title);
});

it('filters by search term', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create([
        'title' => 'Easy Task',
        'status' => ContentStatus::Approved
    ]);
    $task2 = Task::factory()->create([
        'title' => 'Hard Task',
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('search', 'Easy')
        ->assertSee($task1->title)
        ->assertDontSee($task2->title);
});

it('filters by user type', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create([
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved
    ]);
    $task2 = Task::factory()->create([
        'target_user_type' => TargetUserType::Female,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('userType', TargetUserType::Male->value)
        ->assertSee($task1->title)
        ->assertDontSee($task2->title);
});

it('filters by difficulty level', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create([
        'difficulty_level' => 1,
        'status' => ContentStatus::Approved
    ]);
    $task2 = Task::factory()->create([
        'difficulty_level' => 5,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('difficulty', 1)
        ->assertSee($task1->title)
        ->assertDontSee($task2->title);
});

it('filters premium content', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create([
        'is_premium' => false,
        'status' => ContentStatus::Approved
    ]);
    $task2 = Task::factory()->create([
        'is_premium' => true,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee($task1->title)
        ->assertDontSee($task2->title);
});

it('shows premium content when enabled', function () {
    $user = User::factory()->create();
    $task1 = Task::factory()->create([
        'is_premium' => false,
        'status' => ContentStatus::Approved
    ]);
    $task2 = Task::factory()->create([
        'is_premium' => true,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('showPremium', true)
        ->assertSee($task1->title)
        ->assertSee($task2->title);
});

it('switches between tasks and outcomes', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['status' => ContentStatus::Approved]);
    $outcome = Outcome::factory()->create(['status' => ContentStatus::Approved]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee($task->title)
        ->assertDontSee($outcome->title)
        ->set('contentType', 'outcomes')
        ->assertDontSee($task->title)
        ->assertSee($outcome->title);
});

it('displays user types filter options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('Male')
        ->assertSee('Female')
        ->assertSee('Couple')
        ->assertSee('Any');
});

it('displays difficulty levels filter options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('Very Easy')
        ->assertSee('Easy')
        ->assertSee('Medium')
        ->assertSee('Hard')
        ->assertSee('Very Hard')
        ->assertSee('Extreme');
});

it('displays content type options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('Tasks')
        ->assertSee('Outcomes');
});

it('resets page when search changes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('search', 'test')
        ->set('search', 'new search')
        ->assertSet('page', 1);
});

it('resets page when user type changes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('userType', TargetUserType::Male->value)
        ->set('userType', TargetUserType::Female->value)
        ->assertSet('page', 1);
});

it('resets page when difficulty changes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('difficulty', 1)
        ->set('difficulty', 2)
        ->assertSet('page', 1);
});

it('resets page when premium filter changes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('showPremium', true)
        ->set('showPremium', false)
        ->assertSet('page', 1);
});

it('resets page when content type changes', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('contentType', 'outcomes')
        ->set('contentType', 'tasks')
        ->assertSet('page', 1);
});

it('clears all filters', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('search', 'test')
        ->set('userType', TargetUserType::Male->value)
        ->set('difficulty', 1)
        ->set('showPremium', true)
        ->set('contentType', 'outcomes')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('userType', null)
        ->assertSet('difficulty', null)
        ->assertSet('showPremium', false)
        ->assertSet('contentType', 'tasks')
        ->assertSet('page', 1);
});

it('displays pagination', function () {
    $user = User::factory()->create();
    
    // Create more tasks than the pagination limit
    Task::factory()->count(15)->create(['status' => ContentStatus::Approved]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('pagination', false);
});

it('shows task details', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'difficulty_level' => 3,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee($task->title)
        ->assertSee($task->description)
        ->assertSee('Medium');
});

it('shows outcome details when viewing outcomes', function () {
    $user = User::factory()->create();
    $outcome = Outcome::factory()->create([
        'title' => 'Test Outcome',
        'description' => 'Test Description',
        'intended_type' => 'reward',
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('contentType', 'outcomes')
        ->assertSee($outcome->title)
        ->assertSee($outcome->description)
        ->assertSee('Reward');
});

it('displays author information', function () {
    $user = User::factory()->create();
    $author = User::factory()->create(['name' => 'John Doe']);
    $task = Task::factory()->create([
        'user_id' => $author->id,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('John Doe');
});

it('shows view count', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'view_count' => 42,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('42 views');
});

it('displays creation date', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'created_at' => now()->subDays(2),
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertSee('2 days ago');
});

it('shows premium badge for premium content', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'is_premium' => true,
        'status' => ContentStatus::Approved
    ]);
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->set('showPremium', true)
        ->assertSee('Premium');
});

it('handles unauthenticated users', function () {
    $this->get('/app/tasks/browse')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertViewIs('livewire.tasks.browse-tasks');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(BrowseTasks::class)
        ->assertStatus(200);
});

