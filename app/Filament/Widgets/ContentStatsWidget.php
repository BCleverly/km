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
                ->descriptionIcon('heroicon-m-document')
                ->color('primary'),
            
            Stat::make('Total Fantasies', Fantasy::count())
                ->description('All fantasies')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),
            
            Stat::make('Total Comments', Comment::count())
                ->description('All comments')
                ->descriptionIcon('heroicon-m-chat-bubble-oval-left-ellipsis')
                ->color('primary'),
        ];
    }
}
