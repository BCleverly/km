<?php

namespace App\Filament\Resources\Tasks\Outcomes;

use App\Filament\Resources\Tasks\Outcomes\Pages\CreateOutcome;
use App\Filament\Resources\Tasks\Outcomes\Pages\EditOutcome;
use App\Filament\Resources\Tasks\Outcomes\Pages\ListOutcomes;
use App\Filament\Resources\Tasks\Outcomes\Schemas\OutcomeForm;
use App\Filament\Resources\Tasks\Outcomes\Tables\OutcomesTable;
use App\Models\Tasks\Outcome;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OutcomeResource extends Resource
{
    protected static ?string $model = Outcome::class;

    protected static ?string $navigationIcon = Heroicon::OutlineGift;

    protected static ?string $navigationLabel = 'Outcomes';

    protected static ?string $modelLabel = 'Outcome';

    protected static ?string $pluralModelLabel = 'Outcomes';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return OutcomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutcomesTable::configure($table);
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
            'index' => ListOutcomes::route('/'),
            'create' => CreateOutcome::route('/create'),
            'edit' => EditOutcome::route('/{record}/edit'),
        ];
    }
}
