<?php

namespace App\Filament\Resources\Models\Tags;

use App\Filament\Resources\Models\Tags\Pages\CreateTag;
use App\Filament\Resources\Models\Tags\Pages\EditTag;
use App\Filament\Resources\Models\Tags\Pages\ListTags;
use App\Filament\Resources\Models\Tags\Schemas\TagForm;
use App\Filament\Resources\Models\Tags\Tables\TagsTable;
use App\Models\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = Heroicon::OutlineTag;

    protected static ?string $navigationLabel = 'Tags';

    protected static ?string $modelLabel = 'Tag';

    protected static ?string $pluralModelLabel = 'Tags';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
