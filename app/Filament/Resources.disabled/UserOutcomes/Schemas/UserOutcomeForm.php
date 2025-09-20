<?php

namespace App\Filament\Resources\UserOutcomes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserOutcomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('outcome_type')
                    ->required(),
                Select::make('outcome_id')
                    ->relationship('outcome', 'title')
                    ->required(),
                Select::make('task_id')
                    ->relationship('task', 'title'),
                TextInput::make('user_assigned_task_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('assigned_at')
                    ->required(),
                DateTimePicker::make('completed_at'),
                DateTimePicker::make('expires_at'),
            ]);
    }
}
