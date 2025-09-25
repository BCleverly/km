<?php

namespace App\Filament\Resources\UserAssignedTasks\Pages;

use App\Filament\Resources\UserAssignedTasks\UserAssignedTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserAssignedTask extends CreateRecord
{
    protected static string $resource = UserAssignedTaskResource::class;
}
