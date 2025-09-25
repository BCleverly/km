<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register search strategy
        $this->app->bind(
            \App\Contracts\SearchStrategyInterface::class,
            \App\Services\Search\MySqlSearchStrategy::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();

        Cashier::calculateTaxes();

        // Using closure-based composers...

        Facades\View::composer('components.layouts.app', function (View $view) {
            $user = auth()->user();

            $topNav = [
                ['text' => 'Dashboard', 'link' => route('app.dashboard'), 'icon' => 'icon.home'],
                ['text' => 'Tasks', 'link' => route('app.tasks'), 'icon' => 'icon.checkmark'],
                ['text' => 'Fantasies', 'link' => route('app.fantasies.index'), 'icon' => 'icon.heart'],
                ['text' => 'Stories', 'link' => route('app.stories.index'), 'icon' => 'icon.book'],
                ['text' => 'Search', 'link' => route('app.search'), 'icon' => 'icon.magnifying-glass'],
            ];

            // Add Desire Discovery for users with partners or admins
            if ($user && ($user->partner || $user->hasRole('Admin'))) {
                $topNav[] = ['text' => 'Desire Discovery', 'link' => route('app.desire-discovery.explore'), 'icon' => 'icon.sparkles'];
            }

            // Only show billing link for non-lifetime users
            if ($user && ! $user->hasLifetimeSubscription()) {
                $topNav[] = ['text' => 'Billing', 'link' => route('app.subscription.billing'), 'icon' => 'icon.credit-card'];
            }

            $view->with('topNav', $topNav);

            $bottomNav = [
                ['text' => 'Task Community', 'link' => route('app.tasks.community'), 'icon' => 'icon.checkmark'],
            ];

            // Add Desire Discovery community for users with partners or admins
            if ($user && ($user->partner || $user->hasRole('Admin'))) {
                $bottomNav[] = ['text' => 'Submit Desire Item', 'link' => route('app.desire-discovery.submit'), 'icon' => 'icon.sparkles'];
            }

            $view->with('bottomNav', $bottomNav);

        });
    }
}
