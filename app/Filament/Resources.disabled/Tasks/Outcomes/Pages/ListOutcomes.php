<?php

namespace App\Filament\Resources\Tasks\Outcomes\Pages;

use App\Filament\Resources\Tasks\Outcomes\OutcomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOutcomes extends ListRecords
{
    protected static string $resource = OutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
