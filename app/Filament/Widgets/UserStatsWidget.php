<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Tasks\UserAssignedTask;
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
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            
            Stat::make('Recent Users', User::where('created_at', '>=', now()->subDays(7))->count())
                ->description('New users in last 7 days')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),
            
            Stat::make('Pending Tasks', UserAssignedTask::where('status', TaskStatus::Assigned)->count())
                ->description('Tasks awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
