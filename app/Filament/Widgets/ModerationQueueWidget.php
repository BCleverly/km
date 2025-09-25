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
                ->color('warning'),

            Stat::make('Pending Fantasies', Fantasy::where('status', 'pending')->count())
                ->description('Fantasies awaiting review')
                ->color('warning'),

            Stat::make('Pending Comments', Comment::where('is_approved', false)->count())
                ->description('Comments awaiting approval')
                ->color('warning'),
        ];
    }
}
