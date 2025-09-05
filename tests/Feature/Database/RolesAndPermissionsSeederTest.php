<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('creates all required roles', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $expectedRoles = ['User', 'Reviewer', 'Moderator', 'Admin'];
    
    foreach ($expectedRoles as $roleName) {
        expect(Role::where('name', $roleName)->exists())->toBeTrue();
    }
});

it('creates all required permissions', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $expectedPermissions = [
        'get tasks assigned',
        'submit content',
        'react to content',
        'manage subscription',
        'view own profile',
        'update own profile',
        'view tasks',
        'view rewards',
        'view punishments',
        'view stories',
        'create tasks',
        'create rewards',
        'create punishments',
        'create stories',
        'edit own content',
        'delete own content',
        'access review queue',
        'vote on content',
        'view pending content',
        'approve content',
        'reject content',
        'edit any content',
        'delete any content',
        'ban users',
        'suspend users',
        'view user details',
        'view activity logs',
        'manage user roles',
        'view subscription data',
        'issue refunds',
        'full crud all data',
        'view system logs',
        'manage system settings',
        'access admin panel',
    ];
    
    foreach ($expectedPermissions as $permissionName) {
        expect(Permission::where('name', $permissionName)->exists())->toBeTrue();
    }
});

it('assigns correct permissions to User role', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $userRole = Role::where('name', 'User')->first();
    
    expect($userRole->permissions)->toHaveCount(14);
    expect($userRole->hasPermissionTo('get tasks assigned'))->toBeTrue();
    expect($userRole->hasPermissionTo('submit content'))->toBeTrue();
    expect($userRole->hasPermissionTo('react to content'))->toBeTrue();
    expect($userRole->hasPermissionTo('manage subscription'))->toBeTrue();
    expect($userRole->hasPermissionTo('view own profile'))->toBeTrue();
    expect($userRole->hasPermissionTo('update own profile'))->toBeTrue();
    expect($userRole->hasPermissionTo('view tasks'))->toBeTrue();
    expect($userRole->hasPermissionTo('view rewards'))->toBeTrue();
    expect($userRole->hasPermissionTo('view punishments'))->toBeTrue();
    expect($userRole->hasPermissionTo('create tasks'))->toBeTrue();
    expect($userRole->hasPermissionTo('create rewards'))->toBeTrue();
    expect($userRole->hasPermissionTo('create punishments'))->toBeTrue();
    expect($userRole->hasPermissionTo('edit own content'))->toBeTrue();
    expect($userRole->hasPermissionTo('delete own content'))->toBeTrue();
    
    // User should not have admin permissions
    expect($userRole->hasPermissionTo('access admin panel'))->toBeFalse();
    expect($userRole->hasPermissionTo('manage user roles'))->toBeFalse();
});

it('assigns correct permissions to Reviewer role', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $reviewerRole = Role::where('name', 'Reviewer')->first();
    
    expect($reviewerRole->permissions)->toHaveCount(17);
    
    // Should have all User permissions
    expect($reviewerRole->hasPermissionTo('get tasks assigned'))->toBeTrue();
    expect($reviewerRole->hasPermissionTo('submit content'))->toBeTrue();
    expect($reviewerRole->hasPermissionTo('react to content'))->toBeTrue();
    
    // Should have Reviewer specific permissions
    expect($reviewerRole->hasPermissionTo('access review queue'))->toBeTrue();
    expect($reviewerRole->hasPermissionTo('vote on content'))->toBeTrue();
    expect($reviewerRole->hasPermissionTo('view pending content'))->toBeTrue();
    
    // Should not have Moderator permissions
    expect($reviewerRole->hasPermissionTo('approve content'))->toBeFalse();
    expect($reviewerRole->hasPermissionTo('ban users'))->toBeFalse();
});

it('assigns correct permissions to Moderator role', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $moderatorRole = Role::where('name', 'Moderator')->first();
    
    expect($moderatorRole->permissions)->toHaveCount(25);
    
    // Should have all User and Reviewer permissions
    expect($moderatorRole->hasPermissionTo('get tasks assigned'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('access review queue'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('vote on content'))->toBeTrue();
    
    // Should have Moderator specific permissions
    expect($moderatorRole->hasPermissionTo('approve content'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('reject content'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('edit any content'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('delete any content'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('ban users'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('suspend users'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('view user details'))->toBeTrue();
    expect($moderatorRole->hasPermissionTo('view activity logs'))->toBeTrue();
    
    // Should not have Admin permissions
    expect($moderatorRole->hasPermissionTo('manage user roles'))->toBeFalse();
    expect($moderatorRole->hasPermissionTo('issue refunds'))->toBeFalse();
});

it('assigns all permissions to Admin role', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $adminRole = Role::where('name', 'Admin')->first();
    
    expect($adminRole->permissions)->toHaveCount(34);
    
    // Should have all permissions
    expect($adminRole->hasPermissionTo('get tasks assigned'))->toBeTrue();
    expect($adminRole->hasPermissionTo('access review queue'))->toBeTrue();
    expect($adminRole->hasPermissionTo('approve content'))->toBeTrue();
    expect($adminRole->hasPermissionTo('ban users'))->toBeTrue();
    expect($adminRole->hasPermissionTo('manage user roles'))->toBeTrue();
    expect($adminRole->hasPermissionTo('view subscription data'))->toBeTrue();
    expect($adminRole->hasPermissionTo('issue refunds'))->toBeTrue();
    expect($adminRole->hasPermissionTo('full crud all data'))->toBeTrue();
    expect($adminRole->hasPermissionTo('access admin panel'))->toBeTrue();
});

it('can be run multiple times without errors', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    
    // Should still have the correct number of roles and permissions
    expect(Role::count())->toBe(4);
    expect(Permission::count())->toBe(34);
});
