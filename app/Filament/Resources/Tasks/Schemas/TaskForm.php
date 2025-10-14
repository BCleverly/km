<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\AffiliateLink;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Task Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('description')
                            ->required()
                            ->rows(4),
                        
                        TextInput::make('difficulty_level')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->required(),
                        
                        TextInput::make('duration_time')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        
                        Select::make('duration_type')
                            ->options([
                                'minutes' => 'Minutes',
                                'hours' => 'Hours',
                                'days' => 'Days',
                                'weeks' => 'Weeks',
                            ])
                            ->required(),
                        
                        Select::make('target_user_type')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'couple' => 'Couple',
                                'any' => 'Any',
                            ])
                            ->required(),
                        
                        Toggle::make('is_premium')
                            ->label('Premium Task'),
                    ])
                    ->columns(2),

                Section::make('Affiliate Links')
                    ->schema([
                        Select::make('affiliate_links')
                            ->multiple()
                            ->relationship(
                                'affiliateLinks',
                                'name',
                                fn (Builder $query) => $query->where('is_active', true)
                            )
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(500),
                                
                                Select::make('partner_type')
                                    ->options(AffiliateLink::getPartnerTypes())
                                    ->required(),
                                
                                Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $affiliateLink = AffiliateLink::create([
                                    ...$data,
                                    'user_id' => auth()->id(),
                                ]);
                                
                                return $affiliateLink->getKey();
                            })
                            ->label('Select Affiliate Partners')
                            ->helperText('Choose affiliate partners that are relevant to this task'),
                    ])
                    ->collapsible(),
            ]);
    }
}
