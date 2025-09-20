<?php

namespace App\Filament\Resources\CoupleTasks;

use App\Filament\Resources\CoupleTasks\Pages\CreateCoupleTask;
use App\Filament\Resources\CoupleTasks\Pages\EditCoupleTask;
use App\Filament\Resources\CoupleTasks\Pages\ListCoupleTasks;
use App\Filament\Resources\CoupleTasks\Schemas\CoupleTaskForm;
use App\Filament\Resources\CoupleTasks\Tables\CoupleTasksTable;
use App\Models\CoupleTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CoupleTaskResource extends Resource
{
    protected static ?string $model = CoupleTask::class;

    protected static ?string $navigationIcon = Heroicon::OutlineHeart;

    protected static ?string $navigationLabel = 'Couple Tasks';

    protected static ?string $modelLabel = 'Couple Task';

    protected static ?string $pluralModelLabel = 'Couple Tasks';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CoupleTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoupleTasksTable::configure($table);
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
            'index' => ListCoupleTasks::route('/'),
            'create' => CreateCoupleTask::route('/create'),
            'edit' => EditCoupleTask::route('/{record}/edit'),
        ];
    }
}
