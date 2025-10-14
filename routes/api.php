<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', App\Actions\Api\Auth\RegisterUser::class);
        Route::post('/login', App\Actions\Api\Auth\LoginUser::class);
    });

    // Public content routes
    Route::prefix('content')->group(function () {
        Route::get('/stories', App\Actions\Api\Content\GetStories::class);
        Route::get('/stories/{slug}', App\Actions\Api\Content\GetStory::class);
        Route::get('/statuses', App\Actions\Api\Content\GetStatuses::class);
        Route::get('/fantasies', App\Actions\Api\Content\GetFantasies::class);
    });

    // Public subscription routes
    Route::prefix('subscription')->group(function () {
        Route::get('/plans', App\Actions\Api\Subscription\GetSubscriptionPlans::class);
    });

    // Search
    Route::get('/search', App\Actions\Api\Search\SearchContent::class);
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', App\Actions\Api\Auth\LogoutUser::class);
        Route::get('/user', App\Actions\Api\User\GetUserProfile::class);
    });

    // User profile routes
    Route::prefix('user')->group(function () {
        Route::put('/profile', App\Actions\Api\User\UpdateUserProfile::class);
    });

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', App\Actions\Api\Tasks\GetUserTasks::class);
        Route::get('/active', App\Actions\Api\Tasks\GetActiveTask::class);
        Route::post('/complete', App\Actions\Api\Tasks\CompleteTask::class);
        Route::get('/stats', App\Actions\Api\Tasks\GetTaskStats::class);
    });

    // Content creation routes
    Route::prefix('content')->group(function () {
        Route::post('/stories', App\Actions\Api\Content\CreateStory::class);
        Route::post('/statuses', App\Actions\Api\Content\CreateStatus::class);
        Route::post('/fantasies', App\Actions\Api\Content\CreateFantasy::class);
    });

    // Subscription management routes
    Route::prefix('subscription')->group(function () {
        Route::get('/current', App\Actions\Api\Subscription\GetUserSubscription::class);
        Route::post('/checkout', App\Actions\Api\Subscription\CreateCheckoutSession::class);
        Route::post('/cancel', App\Actions\Api\Subscription\CancelSubscription::class);
        Route::get('/billing-portal', App\Actions\Api\Subscription\GetBillingPortal::class);
    });

    // Reaction routes
    Route::prefix('reactions')->group(function () {
        Route::post('/toggle', App\Actions\Api\Reactions\ToggleReaction::class);
    });

    // Search (authenticated users get more results)
    Route::get('/search', App\Actions\Api\Search\SearchContent::class);
});
