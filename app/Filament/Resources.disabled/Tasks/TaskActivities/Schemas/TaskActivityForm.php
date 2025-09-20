<?php

namespace App\Filament\Resources\Tasks\TaskActivities\Schemas;

use App\TaskActivityType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TaskActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('task_id')
                    ->relationship('task', 'title')
                    ->required(),
                Select::make('user_assigned_task_id')
                    ->relationship('userAssignedTask', 'id'),
                Select::make('activity_type')
                    ->options(TaskActivityType::class)
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
                DateTimePicker::make('activity_at')
                    ->required(),
            ]);
    }
}
