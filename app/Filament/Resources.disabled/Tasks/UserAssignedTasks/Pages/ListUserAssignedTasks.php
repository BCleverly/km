<?php

namespace App\Filament\Resources\Tasks\UserAssignedTasks\Pages;

use App\Filament\Resources\Tasks\UserAssignedTasks\UserAssignedTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserAssignedTasks extends ListRecords
{
    protected static string $resource = UserAssignedTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
