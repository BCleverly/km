<?php

namespace App\Filament\Resources\Fantasies\Pages;

use App\Filament\Resources\Fantasies\FantasyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFantasy extends EditRecord
{
    protected static string $resource = FantasyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
