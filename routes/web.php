<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard;
use App\Livewire\Homepage;
use App\Livewire\Tasks\Dashboard as TasksDashboard;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;

Route::passkeys();

Route::get('/', Homepage::class);

// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    
    // Password Reset Routes
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Application Routes (Authenticated users only)
Route::middleware('auth')->prefix('app')->name('app.')->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    
    // Tasks
    Route::get('/tasks', TasksDashboard::class)->name('tasks');
});
