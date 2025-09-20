<?php

namespace App\Filament\Resources\Fantasies\Pages;

use App\Filament\Resources\Fantasies\FantasyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFantasies extends ListRecords
{
    protected static string $resource = FantasyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
