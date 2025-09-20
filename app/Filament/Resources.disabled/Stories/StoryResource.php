<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStory;
use App\Filament\Resources\Stories\Pages\EditStory;
use App\Filament\Resources\Stories\Pages\ListStories;
use App\Filament\Resources\Stories\Schemas\StoryForm;
use App\Filament\Resources\Stories\Tables\StoriesTable;
use App\Models\Story;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StoryResource extends Resource
{
    protected static ?string $model = Story::class;

    protected static ?string $navigationIcon = Heroicon::OutlineDocumentText;

    protected static ?string $navigationLabel = 'Stories';

    protected static ?string $modelLabel = 'Story';

    protected static ?string $pluralModelLabel = 'Stories';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return StoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Author' => $record->user->username ?? 'Unknown',
            'Status' => $record->status->value,
            'Views' => $record->views_count,
            'Created' => $record->created_at->format('M j, Y'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content'];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStories::route('/'),
            'create' => CreateStory::route('/create'),
            'edit' => EditStory::route('/{record}/edit'),
        ];
    }
}
