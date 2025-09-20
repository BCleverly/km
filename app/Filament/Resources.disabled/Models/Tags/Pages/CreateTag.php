<?php

namespace App\Filament\Resources\Models\Tags\Pages;

use App\Filament\Resources\Models\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
