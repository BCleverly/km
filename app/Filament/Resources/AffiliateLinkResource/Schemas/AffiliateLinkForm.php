<?php

namespace App\Filament\Resources\AffiliateLinkResource\Schemas;

use App\Models\AffiliateLink;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AffiliateLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                
                                TextInput::make('url')
                                    ->url()
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                
                                Textarea::make('description')
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                                
                                Select::make('user_id')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),
                
                Section::make('Partner Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('partner_type')
                                    ->options(AffiliateLink::getPartnerTypes())
                                    ->required(),
                                
                                TextInput::make('tracking_id')
                                    ->maxLength(255)
                                    ->helperText('Optional tracking ID for analytics'),
                            ]),
                    ]),
                
                Section::make('Commission Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('commission_type')
                                    ->options(AffiliateLink::getCommissionTypes())
                                    ->required()
                                    ->reactive(),
                                
                                TextInput::make('commission_rate')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->visible(fn (callable $get) => $get('commission_type') === 'percentage')
                                    ->required(fn (callable $get) => $get('commission_type') === 'percentage'),
                                
                                TextInput::make('commission_fixed')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->prefix(fn (callable $get) => $get('currency') ?? '$')
                                    ->visible(fn (callable $get) => $get('commission_type') === 'fixed')
                                    ->required(fn (callable $get) => $get('commission_type') === 'fixed'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('currency')
                                    ->options([
                                        'USD' => 'USD ($)',
                                        'EUR' => 'EUR (€)',
                                        'GBP' => 'GBP (£)',
                                        'CAD' => 'CAD (C$)',
                                        'AUD' => 'AUD (A$)',
                                    ])
                                    ->default('USD')
                                    ->required(),
                                
                                Textarea::make('notes')
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                
                Section::make('Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->helperText('Whether this affiliate link is active'),
                                
                                Toggle::make('is_premium')
                                    ->helperText('Whether this is a premium affiliate link'),
                            ]),
                    ]),
            ]);
    }
}
