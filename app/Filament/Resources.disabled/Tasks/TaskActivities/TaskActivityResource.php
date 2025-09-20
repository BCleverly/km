<?php

namespace App\Filament\Resources\Tasks\TaskActivities;

use App\Filament\Resources\Tasks\TaskActivities\Pages\CreateTaskActivity;
use App\Filament\Resources\Tasks\TaskActivities\Pages\EditTaskActivity;
use App\Filament\Resources\Tasks\TaskActivities\Pages\ListTaskActivities;
use App\Filament\Resources\Tasks\TaskActivities\Schemas\TaskActivityForm;
use App\Filament\Resources\Tasks\TaskActivities\Tables\TaskActivitiesTable;
use App\Models\Tasks\TaskActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaskActivityResource extends Resource
{
    protected static ?string $model = TaskActivity::class;

    protected static ?string $navigationIcon = Heroicon::OutlineClock;

    protected static ?string $navigationLabel = 'Task Activities';

    protected static ?string $modelLabel = 'Task Activity';

    protected static ?string $pluralModelLabel = 'Task Activities';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TaskActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskActivitiesTable::configure($table);
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
            'index' => ListTaskActivities::route('/'),
            'create' => CreateTaskActivity::route('/create'),
            'edit' => EditTaskActivity::route('/{record}/edit'),
        ];
    }
}
