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
use App\Livewire\User\PublicProfile;
use App\Livewire\User\Settings;
use App\Livewire\Fantasies\ListFantasies;
use App\Livewire\Fantasies\CreateFantasy;
use App\Livewire\Stories\ListStories;
use App\Livewire\Stories\ShowStory;
use App\Livewire\Stories\CreateStory;
use App\Livewire\Comments\CommentsDemo;
use App\Livewire\Search\SearchContent;
use Illuminate\Support\Facades\Route;

Route::passkeys();

// Stripe Webhook (must be outside middleware)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

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
    
    // Profile & Settings
    Route::get('/profile/{username}', PublicProfile::class)->name('profile');
    Route::get('/settings', Settings::class)->name('settings');

    // Tasks
    Route::get('/tasks', TasksDashboard::class)->name('tasks');
    Route::get('/tasks/create', CreateCustomTask::class)->name('tasks.create');

    Route::get('/tasks/community', \App\Livewire\Tasks\TaskCommunityDashboard::class)->name('tasks.community');

    // Couple Tasks
    Route::prefix('couple-tasks')->name('couple-tasks.')->group(function () {
        Route::get('/send', \App\Livewire\CoupleTasks\SendTask::class)->name('send');
        Route::get('/my-tasks', \App\Livewire\CoupleTasks\MyTasks::class)->name('my-tasks');
    });

    // Fantasies
    Route::get('/fantasies', ListFantasies::class)->name('fantasies.index');
    Route::get('/fantasies/create', CreateFantasy::class)->name('fantasies.create');

    // Stories
    Route::get('/stories', ListStories::class)->name('stories.index');
    Route::get('/stories/create', CreateStory::class)->name('stories.create');
    Route::get('/stories/{story:slug}', ShowStory::class)->name('stories.show');

    // Comments Demo
    Route::get('/comments-demo', CommentsDemo::class)->name('comments.demo');

    // Search
    Route::get('/search', SearchContent::class)->name('search');

    // Subscription Routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/choose', \App\Livewire\Subscription\ChoosePlan::class)->name('choose');
        Route::get('/success', \App\Livewire\Subscription\Success::class)->name('success');
        Route::get('/cancel', \App\Livewire\Subscription\Cancel::class)->name('cancel');
        Route::get('/billing', \App\Livewire\Subscription\Billing::class)->name('billing');
    });
});
