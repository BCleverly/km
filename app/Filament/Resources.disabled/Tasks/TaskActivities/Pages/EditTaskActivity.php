<?php

namespace App\Filament\Resources\Tasks\TaskActivities\Pages;

use App\Filament\Resources\Tasks\TaskActivities\TaskActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskActivity extends EditRecord
{
    protected static string $resource = TaskActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
