<?php

namespace App\Filament\Resources\Tasks\UserAssignedTasks\Schemas;

use App\TaskStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserAssignedTaskForm
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
                Select::make('status')
                    ->options(TaskStatus::class)
                    ->default(1)
                    ->required(),
                TextInput::make('outcome_type'),
                Select::make('outcome_id')
                    ->relationship('outcome', 'title'),
                Select::make('potential_reward_id')
                    ->relationship('potentialReward', 'title'),
                Select::make('potential_punishment_id')
                    ->relationship('potentialPunishment', 'title'),
                DateTimePicker::make('assigned_at')
                    ->required(),
                DateTimePicker::make('deadline_at'),
                DateTimePicker::make('completed_at'),
                Toggle::make('has_completion_image')
                    ->required(),
                Textarea::make('completion_note')
                    ->columnSpanFull(),
            ]);
    }
}
