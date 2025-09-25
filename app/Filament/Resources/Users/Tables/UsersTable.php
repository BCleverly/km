<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\SubscriptionPlan;
use App\TargetUserType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('profile'))
            ->columns([
                // Essential columns (always visible)
                TextColumn::make('username')
                    ->label('Username')
                    ->getStateUsing(function ($record) {
                        // Check profile username first, then fallback to user table username
                        return $record->profile?->username ?? $record->username ?? 'No username';
                    })
                    ->searchable(['username', 'profiles.username'])
                    ->sortable(),
                BadgeColumn::make('user_type')
                    ->label('User Type')
                    ->formatStateUsing(fn (TargetUserType $state): string => $state->label())
                    ->colors([
                        'primary' => TargetUserType::Male,
                        'success' => TargetUserType::Female,
                        'warning' => TargetUserType::Couple,
                        'gray' => TargetUserType::Any,
                    ])
                    ->sortable(),
                BadgeColumn::make('subscription_plan')
                    ->label('Subscription Plan')
                    ->formatStateUsing(fn (SubscriptionPlan $state): string => $state->label())
                    ->colors([
                        'gray' => SubscriptionPlan::Free,
                        'primary' => SubscriptionPlan::Solo,
                        'success' => SubscriptionPlan::Premium,
                        'warning' => SubscriptionPlan::Couple,
                        'purple' => SubscriptionPlan::Lifetime,
                    ])
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label('Partner')
                    ->searchable()
                    ->placeholder('No partner'),

                // Toggleable columns (hidden by default)
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stripe_id')
                    ->label('Stripe ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pm_type')
                    ->label('Payment Method Type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pm_last_four')
                    ->label('Payment Method Last 4')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('trial_ends_at')
                    ->label('Trial Ends At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subscription_ends_at')
                    ->label('Subscription Ends At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_used_trial')
                    ->label('Used Trial')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->options([
                        TargetUserType::Male->value => TargetUserType::Male->label(),
                        TargetUserType::Female->value => TargetUserType::Female->label(),
                        TargetUserType::Couple->value => TargetUserType::Couple->label(),
                        TargetUserType::Any->value => TargetUserType::Any->label(),
                    ]),
                SelectFilter::make('subscription_plan')
                    ->options([
                        SubscriptionPlan::Free->value => SubscriptionPlan::Free->label(),
                        SubscriptionPlan::Solo->value => SubscriptionPlan::Solo->label(),
                        SubscriptionPlan::Premium->value => SubscriptionPlan::Premium->label(),
                        SubscriptionPlan::Couple->value => SubscriptionPlan::Couple->label(),
                        SubscriptionPlan::Lifetime->value => SubscriptionPlan::Lifetime->label(),
                    ]),
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable(),
                TernaryFilter::make('has_used_trial')
                    ->label('Used Trial'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('app.profile', ['username' => $record->profile?->username ?? $record->username ?? $record->id]))
                    ->openUrlInNewTab()
                    ->label('View Profile'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
