<?php

namespace App\Filament\Resources\AffiliateLinkResource\Tables;

use App\Models\AffiliateLink;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AffiliateLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('url')
                    ->limit(50)
                    ->copyable()
                    ->copyMessage('URL copied')
                    ->copyMessageDuration(1500),
                
                TextColumn::make('partner_type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'toys' => 'pink',
                        'clothing' => 'purple',
                        'books' => 'blue',
                        'health' => 'green',
                        'subscription' => 'orange',
                        default => 'gray',
                    }),
                
                TextColumn::make('commission_display')
                    ->label('Commission')
                    ->getStateUsing(fn (AffiliateLink $record) => $record->formatted_commission_rate)
                    ->sortable(),
                
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                IconColumn::make('is_premium')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
                
                TernaryFilter::make('is_premium')
                    ->label('Premium Status'),
                
                SelectFilter::make('partner_type')
                    ->options(AffiliateLink::getPartnerTypes()),
                
                SelectFilter::make('commission_type')
                    ->options(AffiliateLink::getCommissionTypes()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
