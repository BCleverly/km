<?php

namespace App\Filament\Resources\CoupleTasks\Pages;

use App\Filament\Resources\CoupleTasks\CoupleTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoupleTask extends EditRecord
{
    protected static string $resource = CoupleTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
