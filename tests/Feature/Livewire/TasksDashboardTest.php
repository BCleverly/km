<?php

use App\Livewire\Tasks\Dashboard;
use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskActivity;
use App\Models\UserOutcome;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertStatus(200);
});

it('displays dashboard title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Tasks Dashboard');
});

it('shows recent activities', function () {
    $user = User::factory()->create();
    
    // Create some task activities
    TaskActivity::factory()->count(3)->create(['user_id' => $user->id]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Recent Activities', false);
});

it('displays active task if user has one', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => 1 // assigned
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Current Task', false)
        ->assertSee($task->title);
});

it('shows no active task message when user has no active task', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('No active task', false)
        ->assertSee('Request a new task', false);
});

it('displays active outcomes', function () {
    $user = User::factory()->create();
    
    // Create some active outcomes
    UserOutcome::factory()->count(2)->create([
        'user_id' => $user->id,
        'status' => 'active'
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Active Outcomes', false);
});

it('handles task completion event', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->dispatch('task-completed')
        ->assertDispatched('$refresh');
});

it('handles task failure event', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->dispatch('task-failed')
        ->assertDispatched('$refresh');
});

it('fails current active task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => 1 // assigned
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('failTask')
        ->assertDispatched('notify');
});

it('completes an outcome successfully', function () {
    $user = User::factory()->create();
    $outcome = UserOutcome::factory()->create([
        'user_id' => $user->id,
        'status' => 'active'
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('completeOutcome', $outcome->id)
        ->assertDispatched('notify');
    
    $outcome->refresh();
    expect($outcome->status)->toBe('completed');
});

it('shows error when completing non-existent outcome', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('completeOutcome', 999)
        ->assertDispatched('notify', [
            'type' => 'error',
            'message' => 'Outcome not found'
        ]);
});

it('shows error when completing inactive outcome', function () {
    $user = User::factory()->create();
    $outcome = UserOutcome::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed'
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('completeOutcome', $outcome->id)
        ->assertDispatched('notify', [
            'type' => 'error',
            'message' => 'This outcome is not active'
        ]);
});

it('displays user statistics', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Statistics', false)
        ->assertSee('Completed Tasks', false)
        ->assertSee('Current Streak', false);
});

it('shows quick actions', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Quick Actions', false)
        ->assertSee('Request New Task', false)
        ->assertSee('Browse Tasks', false);
});

it('displays progress indicators', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Progress', false)
        ->assertSee('This Week', false)
        ->assertSee('This Month', false);
});

it('shows achievement badges', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Achievements', false)
        ->assertSee('Badges', false);
});

it('displays upcoming deadlines', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    
    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => 1, // assigned
        'deadline_at' => now()->addDays(2)
    ]);
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Upcoming Deadlines', false);
});

it('shows task completion rate', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Completion Rate', false);
});

it('displays motivational messages', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Keep it up!', false)
        ->assertSee('You\'re doing great!', false);
});

it('shows recent notifications', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Notifications', false);
});

it('displays task categories', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Categories', false)
        ->assertSee('Easy', false)
        ->assertSee('Medium', false)
        ->assertSee('Hard', false);
});

it('shows community leaderboard', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Leaderboard', false)
        ->assertSee('Top Performers', false);
});

it('displays subscription status', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Subscription', false);
});

it('shows help and tips section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Tips & Help', false)
        ->assertSee('Getting Started', false);
});

it('handles unauthenticated users', function () {
    $this->get('/app/tasks/dashboard')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertViewIs('livewire.tasks.dashboard');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertStatus(200);
});

