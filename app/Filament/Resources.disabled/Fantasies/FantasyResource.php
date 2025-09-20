<?php

namespace App\Filament\Resources\Fantasies;

use App\Filament\Resources\Fantasies\Pages\CreateFantasy;
use App\Filament\Resources\Fantasies\Pages\EditFantasy;
use App\Filament\Resources\Fantasies\Pages\ListFantasies;
use App\Filament\Resources\Fantasies\Schemas\FantasyForm;
use App\Filament\Resources\Fantasies\Tables\FantasiesTable;
use App\Models\Fantasy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FantasyResource extends Resource
{
    protected static ?string $model = Fantasy::class;

    protected static ?string $navigationIcon = Heroicon::OutlineSparkles;

    protected static ?string $navigationLabel = 'Fantasies';

    protected static ?string $modelLabel = 'Fantasy';

    protected static ?string $pluralModelLabel = 'Fantasies';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FantasyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FantasiesTable::configure($table);
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
            'index' => ListFantasies::route('/'),
            'create' => CreateFantasy::route('/create'),
            'edit' => EditFantasy::route('/{record}/edit'),
        ];
    }
}
