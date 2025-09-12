<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades;
use Illuminate\View\View;

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

            $view->with('topNav', [
                ['text'=> 'Dashboard', 'link' => route('app.dashboard'), 'icon' => 'icon.home'],
                ['text'=> 'Tasks', 'link' => route('app.tasks'), 'icon' => 'icon.checkmark'],
                ['text'=> 'Fantasies','link' =>  route('app.fantasies.index'), 'icon' => 'icon.heart'],
                ['text'=> 'Stories','link' =>  route('app.stories.index'), 'icon' => 'icon.book'],
                ['text'=> 'Search', 'link' => route('app.search'), 'icon' => 'icon.magnifying-glass'],
            ]);

            $view->with('bottomNav', [
                ['text'=> 'Task Community', 'link' =>  route('app.tasks.community'), 'icon' => 'icon.checkmark']
            ]);

        });
    }
}
