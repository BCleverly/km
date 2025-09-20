<?php

namespace App\Filament\Resources\UserOutcomes\Pages;

use App\Filament\Resources\UserOutcomes\UserOutcomeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserOutcome extends EditRecord
{
    protected static string $resource = UserOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
