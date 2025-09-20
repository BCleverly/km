<?php

namespace App\Filament\Resources\Stories\Tables;

use App\ContentStatus;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('word_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reading_time_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->formatStateUsing(fn (ContentStatus $state): string => $state->label())
                    ->colors([
                        'gray' => ContentStatus::Draft,
                        'warning' => ContentStatus::Pending,
                        'success' => ContentStatus::Approved,
                        'orange' => ContentStatus::InReview,
                        'danger' => ContentStatus::Rejected,
                    ])
                    ->sortable(),
                TextColumn::make('report_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_premium')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        ContentStatus::Draft->value => ContentStatus::Draft->label(),
                        ContentStatus::Pending->value => ContentStatus::Pending->label(),
                        ContentStatus::Approved->value => ContentStatus::Approved->label(),
                        ContentStatus::InReview->value => ContentStatus::InReview->label(),
                        ContentStatus::Rejected->value => ContentStatus::Rejected->label(),
                    ]),
                TernaryFilter::make('is_premium')
                    ->label('Premium Stories'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $record->update(['status' => ContentStatus::Approved]);
                        });
                    })
                    ->requiresConfirmation(),
                BulkAction::make('reject')
                    ->label('Reject Selected')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $record->update(['status' => ContentStatus::Rejected]);
                        });
                    })
                    ->requiresConfirmation(),
                BulkAction::make('pending')
                    ->label('Mark as Pending')
                    ->icon('heroicon-m-clock')
                    ->color('warning')
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $record->update(['status' => ContentStatus::Pending]);
                        });
                    })
                    ->requiresConfirmation(),
            ]);
    }
}
