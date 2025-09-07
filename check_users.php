<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Get all users with roles
$users = User::with('roles')->get();

echo "Users in database:\n";
foreach($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n\n";
}
