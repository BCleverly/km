<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use App\TargetUserType;
use App\ContentStatus;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title'),
                Textarea::make('description'),
                Select::make('difficulty_level')
                    ->options([
                        1 => '1 - Very Easy',
                        2 => '2 - Easy',
                        3 => '3 - Medium',
                        4 => '4 - Hard',
                        5 => '5 - Very Hard',
                    ]),
                Select::make('target_user_type')
                    ->options(collect(TargetUserType::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray())
                    ->required(),
                Select::make('status')
                    ->options(collect(ContentStatus::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray())
                    ->required(),
            ]);
    }
}
