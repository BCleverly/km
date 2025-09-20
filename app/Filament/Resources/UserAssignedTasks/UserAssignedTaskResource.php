<?php

namespace App\Filament\Resources\UserAssignedTasks;

use App\Filament\Resources\UserAssignedTasks\Pages\CreateUserAssignedTask;
use App\Filament\Resources\UserAssignedTasks\Pages\EditUserAssignedTask;
use App\Filament\Resources\UserAssignedTasks\Pages\ListUserAssignedTasks;
use App\Filament\Resources\UserAssignedTasks\Schemas\UserAssignedTaskForm;
use App\Filament\Resources\UserAssignedTasks\Tables\UserAssignedTasksTable;
use App\Models\Tasks\UserAssignedTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserAssignedTaskResource extends Resource
{
    protected static ?string $model = UserAssignedTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserAssignedTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserAssignedTasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserAssignedTasks::route('/'),
            'create' => CreateUserAssignedTask::route('/create'),
            'edit' => EditUserAssignedTask::route('/{record}/edit'),
        ];
    }
}
