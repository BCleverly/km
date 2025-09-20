<?php

namespace App\Filament\Resources\Comments;

use App\Models\Comment;
use App\ContentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentResourceDisabled extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = Heroicon::OutlineChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Comments';

    protected static ?string $modelLabel = 'Comment';

    protected static ?string $pluralModelLabel = 'Comments';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('commentable_type')
                    ->required(),
                Forms\Components\TextInput::make('commentable_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'id'),
                Forms\Components\Toggle::make('is_approved')
                    ->required(),
                Forms\Components\DateTimePicker::make('approved_at'),
                Forms\Components\TextInput::make('approved_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Comments\Tables\CommentsTable::configure($table);
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
