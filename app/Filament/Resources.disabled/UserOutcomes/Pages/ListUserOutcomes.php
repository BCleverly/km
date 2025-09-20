<?php

namespace App\Filament\Resources\UserOutcomes\Pages;

use App\Filament\Resources\UserOutcomes\UserOutcomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserOutcomes extends ListRecords
{
    protected static string $resource = UserOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
