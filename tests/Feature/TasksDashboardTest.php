<?php

declare(strict_types=1);

use App\Models\User;

it('can access tasks dashboard when authenticated', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get(route('app.tasks'))
        ->assertSuccessful()
        ->assertSeeLivewire('tasks.dashboard');
});

it('redirects unauthenticated users to login', function () {
    $this->get(route('app.tasks'))
        ->assertRedirect(route('login'));
});

it('displays tasks dashboard content', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get(route('app.tasks'))
        ->assertSuccessful()
        ->assertSee('Tasks Dashboard')
        ->assertSee('Complete tasks to earn rewards and discover new experiences')
        ->assertSee('No Active Task')
        ->assertSee('Get New Task')
        ->assertSee('Tasks Completed')
        ->assertSee('Rewards Earned')
        ->assertSee('Current Streak')
        ->assertSee('Recent Activity');
});

it('shows empty state when no tasks are assigned', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get(route('app.tasks'))
        ->assertSuccessful()
        ->assertSee('No Active Task')
        ->assertSee('Ready for a new challenge?')
        ->assertSee('No activity yet')
        ->assertSee('Complete your first task to see your activity history here');
});

it('displays correct page title', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get(route('app.tasks'))
        ->assertSuccessful()
        ->assertSee('Tasks Dashboard');
});