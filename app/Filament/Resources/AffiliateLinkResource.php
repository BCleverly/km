<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateLinkResource\Pages;
use App\Models\AffiliateLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliateLinkResource extends Resource
{
    protected static ?string $model = AffiliateLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Partner Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Lovehoney, Adam & Eve'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Brief description of the partner and their products'),
                        
                        Forms\Components\Select::make('partner_type')
                            ->options(AffiliateLink::getPartnerTypes())
                            ->required()
                            ->default('general'),
                        
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/affiliate-link'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Commission Settings')
                    ->schema([
                        Forms\Components\Select::make('commission_type')
                            ->options(AffiliateLink::getCommissionTypes())
                            ->required()
                            ->default('percentage')
                            ->live(),
                        
                        Forms\Components\TextInput::make('commission_rate')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (Forms\Get $get) => $get('commission_type') === 'percentage')
                            ->required(fn (Forms\Get $get) => $get('commission_type') === 'percentage'),
                        
                        Forms\Components\TextInput::make('commission_fixed')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('$')
                            ->visible(fn (Forms\Get $get) => $get('commission_type') === 'fixed')
                            ->required(fn (Forms\Get $get) => $get('commission_type') === 'fixed'),
                        
                        Forms\Components\TextInput::make('currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->placeholder('USD'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                        
                        Forms\Components\Toggle::make('is_premium')
                            ->default(false)
                            ->label('Premium Partner'),
                        
                        Forms\Components\TextInput::make('tracking_id')
                            ->maxLength(255)
                            ->placeholder('Partner tracking ID'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Internal notes about this partner'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('partner_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'toys' => 'success',
                        'clothing' => 'warning',
                        'books' => 'info',
                        'general' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('formatted_commission_rate')
                    ->label('Commission')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\IconColumn::make('is_premium')
                    ->boolean()
                    ->label('Premium'),
                
                Tables\Columns\TextColumn::make('tasks_count')
                    ->counts('tasks')
                    ->label('Tasks'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('partner_type')
                    ->options(AffiliateLink::getPartnerTypes()),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                
                Tables\Filters\TernaryFilter::make('is_premium')
                    ->label('Premium'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAffiliateLinks::route('/'),
            'create' => Pages\CreateAffiliateLink::route('/create'),
            'edit' => Pages\EditAffiliateLink::route('/{record}/edit'),
        ];
    }
}