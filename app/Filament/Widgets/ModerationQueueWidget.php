<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Fantasy;
use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ModerationQueueWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Stories', Story::where('status', 'pending')->count())
                ->description('Stories awaiting review')
                ->descriptionIcon('heroicon-m-document')
                ->color('warning'),
            
            Stat::make('Pending Fantasies', Fantasy::where('status', 'pending')->count())
                ->description('Fantasies awaiting review')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),
            
            Stat::make('Pending Comments', Comment::where('is_approved', false)->count())
                ->description('Comments awaiting approval')
                ->descriptionIcon('heroicon-m-chat-bubble-oval-left-ellipsis')
                ->color('warning'),
        ];
    }
}
