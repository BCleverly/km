<?php

namespace App\Filament\Resources\CoupleTasks\Schemas;

use App\Enums\CoupleTaskStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CoupleTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('assigned_by')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_to')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('dom_message')
                    ->columnSpanFull(),
                TextInput::make('difficulty_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('duration_hours')
                    ->required()
                    ->numeric()
                    ->default(24),
                Select::make('status')
                    ->options(CoupleTaskStatus::class)
                    ->default(1)
                    ->required(),
                Select::make('reward_id')
                    ->relationship('reward', 'title'),
                Select::make('punishment_id')
                    ->relationship('punishment', 'title'),
                DateTimePicker::make('assigned_at')
                    ->required(),
                DateTimePicker::make('deadline_at'),
                DateTimePicker::make('completed_at'),
                Textarea::make('completion_notes')
                    ->columnSpanFull(),
                Textarea::make('thank_you_message')
                    ->columnSpanFull(),
                DateTimePicker::make('thanked_at'),
            ]);
    }
}
