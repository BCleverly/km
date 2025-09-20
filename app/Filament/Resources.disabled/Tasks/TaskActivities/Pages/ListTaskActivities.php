<?php

namespace App\Filament\Resources\Tasks\TaskActivities\Pages;

use App\Filament\Resources\Tasks\TaskActivities\TaskActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskActivities extends ListRecords
{
    protected static string $resource = TaskActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
