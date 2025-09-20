<?php

namespace App\Filament\Resources\UserAssignedTasks\Pages;

use App\Filament\Resources\UserAssignedTasks\UserAssignedTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserAssignedTask extends EditRecord
{
    protected static string $resource = UserAssignedTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
