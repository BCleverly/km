<?php

namespace App\Filament\Resources\Tasks\Outcomes\Schemas;

use App\ContentStatus;
use App\TargetUserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OutcomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('difficulty_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('target_user_type')
                    ->options(TargetUserType::class)
                    ->default(4)
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default(1)
                    ->required(),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_premium')
                    ->required(),
                TextInput::make('intended_type')
                    ->required(),
            ]);
    }
}
