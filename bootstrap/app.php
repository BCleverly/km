<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        //        $schedule->command('activitylog:clean --days=7')->daily();
        
        // Sync view counts from Redis to database daily at 00:01
        $schedule->command('views:sync')->dailyAt('00:01');
    })
    ->withCommands([
        // Register custom production setup commands
        \App\Console\Commands\SetupProduction::class,
        \App\Console\Commands\CreateAdminUser::class,
        \App\Console\Commands\SyncViewCountsCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
