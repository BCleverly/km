<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'get tasks assigned',
            'submit content',
            'react to content',
            'manage subscription',
            'view own profile',
            'update own profile',
            
            // Content permissions
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
            
            // Reviewer permissions
            'access review queue',
            'vote on content',
            'view pending content',
            
            // Moderator permissions
            'approve content',
            'reject content',
            'edit any content',
            'delete any content',
            'ban users',
            'suspend users',
            'view user details',
            'view activity logs',
            
            // Admin permissions
            'manage user roles',
            'view subscription data',
            'issue refunds',
            'full crud all data',
            'view system logs',
            'manage system settings',
            'access admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // User role (default)
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->givePermissionTo([
            'get tasks assigned',
            'submit content',
            'react to content',
            'manage subscription',
            'view own profile',
            'update own profile',
            'view tasks',
            'view rewards',
            'view punishments',
            'create tasks',
            'create rewards',
            'create punishments',
            'edit own content',
            'delete own content',
        ]);

        // Reviewer role
        $reviewerRole = Role::firstOrCreate(['name' => 'Reviewer']);
        $reviewerRole->givePermissionTo([
            // Inherit all User permissions
            'get tasks assigned',
            'submit content',
            'react to content',
            'manage subscription',
            'view own profile',
            'update own profile',
            'view tasks',
            'view rewards',
            'view punishments',
            'create tasks',
            'create rewards',
            'create punishments',
            'edit own content',
            'delete own content',
            
            // Reviewer specific permissions
            'access review queue',
            'vote on content',
            'view pending content',
        ]);

        // Moderator role
        $moderatorRole = Role::firstOrCreate(['name' => 'Moderator']);
        $moderatorRole->givePermissionTo([
            // Inherit all Reviewer permissions
            'get tasks assigned',
            'submit content',
            'react to content',
            'manage subscription',
            'view own profile',
            'update own profile',
            'view tasks',
            'view rewards',
            'view punishments',
            'create tasks',
            'create rewards',
            'create punishments',
            'edit own content',
            'delete own content',
            'access review queue',
            'vote on content',
            'view pending content',
            
            // Moderator specific permissions
            'approve content',
            'reject content',
            'edit any content',
            'delete any content',
            'ban users',
            'suspend users',
            'view user details',
            'view activity logs',
        ]);

        // Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            // All permissions
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
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: User, Reviewer, Moderator, Admin');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
