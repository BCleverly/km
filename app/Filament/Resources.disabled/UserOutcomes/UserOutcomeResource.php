<?php

namespace App\Filament\Resources\UserOutcomes;

use App\Filament\Resources\UserOutcomes\Pages\CreateUserOutcome;
use App\Filament\Resources\UserOutcomes\Pages\EditUserOutcome;
use App\Filament\Resources\UserOutcomes\Pages\ListUserOutcomes;
use App\Filament\Resources\UserOutcomes\Schemas\UserOutcomeForm;
use App\Filament\Resources\UserOutcomes\Tables\UserOutcomesTable;
use App\Models\UserOutcome;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserOutcomeResource extends Resource
{
    protected static ?string $model = UserOutcome::class;

    protected static ?string $navigationIcon = Heroicon::OutlineTrophy;

    protected static ?string $navigationLabel = 'User Outcomes';

    protected static ?string $modelLabel = 'User Outcome';

    protected static ?string $pluralModelLabel = 'User Outcomes';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return UserOutcomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserOutcomesTable::configure($table);
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
            'index' => ListUserOutcomes::route('/'),
            'create' => CreateUserOutcome::route('/create'),
            'edit' => EditUserOutcome::route('/{record}/edit'),
        ];
    }
}
