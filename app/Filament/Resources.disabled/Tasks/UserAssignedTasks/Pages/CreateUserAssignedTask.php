<?php

namespace App\Filament\Resources\Tasks\UserAssignedTasks\Pages;

use App\Filament\Resources\Tasks\UserAssignedTasks\UserAssignedTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserAssignedTask extends CreateRecord
{
    protected static string $resource = UserAssignedTaskResource::class;
}
