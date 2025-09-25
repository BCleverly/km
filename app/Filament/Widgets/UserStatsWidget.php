<?php

namespace App\Filament\Widgets;

use App\Models\Tasks\UserAssignedTask;
use App\Models\User;
use App\TaskStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->color('primary'),

            Stat::make('Recent Users', User::where('created_at', '>=', now()->subDays(7))->count())
                ->description('New users in last 7 days')
                ->color('success'),

            Stat::make('Pending Tasks', UserAssignedTask::where('status', TaskStatus::Assigned)->count())
                ->description('Tasks awaiting completion')
                ->color('warning'),
        ];
    }
}
