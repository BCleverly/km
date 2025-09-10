<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard;
use App\Livewire\Homepage;
use App\Livewire\Tasks\Dashboard as TasksDashboard;
use App\Livewire\Tasks\CreateCustomTask;
use App\Livewire\User\Profile;
use App\Livewire\Fantasies\ListFantasies;
use App\Livewire\Fantasies\CreateFantasy;
use App\Livewire\Stories\ListStories;
use App\Livewire\Stories\ShowStory;
use App\Livewire\Stories\CreateStory;
use App\Http\Controllers\EncryptedMediaController;
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

// Logout Route (Authenticated users only)
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Application Routes (Authenticated users only)
Route::middleware('auth')->prefix('app')->name('app.')->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');

    // Tasks
    Route::get('/tasks', TasksDashboard::class)->name('tasks');
    Route::get('/tasks/create', CreateCustomTask::class)->name('tasks.create');

    Route::get('/tasks/community', \App\Livewire\Tasks\TaskCommunityDashboard::class)->name('tasks.community');

    // Fantasies
    Route::get('/fantasies', ListFantasies::class)->name('fantasies.index');
    Route::get('/fantasies/create', CreateFantasy::class)->name('fantasies.create');

    // Stories
    Route::get('/stories', ListStories::class)->name('stories.index');
    Route::get('/stories/create', CreateStory::class)->name('stories.create');
    Route::get('/stories/{story:slug}', ShowStory::class)->name('stories.show');
});

// Encrypted Media Routes (Authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/media/{media}/encrypted', [EncryptedMediaController::class, 'show'])->name('media.encrypted');
    Route::get('/media/{media}/download', [EncryptedMediaController::class, 'download'])->name('media.download');
});
