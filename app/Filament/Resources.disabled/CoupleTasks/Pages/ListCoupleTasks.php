<?php

namespace App\Filament\Resources\CoupleTasks\Pages;

use App\Filament\Resources\CoupleTasks\CoupleTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoupleTasks extends ListRecords
{
    protected static string $resource = CoupleTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
