<?php

namespace App\Filament\Resources\Models\Tags\Schemas;

use App\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('order_column')
                    ->numeric(),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default(1)
                    ->required(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('approved_by')
                    ->numeric(),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
