<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for task-related services
 */
class TaskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register TaskService factory for manual resolution
        $this->app->bind(TaskService::class, function ($app, $parameters) {
            // If a user is provided in parameters, use it
            if (isset($parameters['user'])) {
                return new TaskService($parameters['user']);
            }
            
            // Otherwise, try to get the authenticated user
            $user = auth()->user();
            if ($user) {
                return new TaskService($user);
            }
            
            // Fallback to creating a new instance (will need user to be set later)
            return new TaskService(new User());
        });

        // Register a factory method for creating TaskService with a specific user
        $this->app->bind('task.service', function ($app) {
            return function (User $user) {
                return new TaskService($user);
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
