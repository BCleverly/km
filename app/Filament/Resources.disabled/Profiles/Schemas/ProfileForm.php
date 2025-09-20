<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Enums\BdsmRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                Textarea::make('about')
                    ->columnSpanFull(),
                Select::make('bdsm_role')
                    ->options(BdsmRole::class),
            ]);
    }
}
