<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateLinkResource\Pages;
use App\Filament\Resources\AffiliateLinkResource\Schemas\AffiliateLinkForm;
use App\Filament\Resources\AffiliateLinkResource\Tables\AffiliateLinksTable;
use App\Models\AffiliateLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AffiliateLinkResource extends Resource
{
    protected static ?string $model = AffiliateLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AffiliateLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AffiliateLinksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateLinks::route('/'),
            'create' => Pages\CreateAffiliateLink::route('/create'),
            'edit' => Pages\EditAffiliateLink::route('/{record}/edit'),
        ];
    }
}
