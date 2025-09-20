<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Fantasy;
use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Stories', Story::count())
                ->description('All stories')
                ->color('primary'),

            Stat::make('Total Fantasies', Fantasy::count())
                ->description('All fantasies')
                ->color('primary'),

            Stat::make('Total Comments', Comment::count())
                ->description('All comments')
                ->color('primary'),
        ];
    }
}
