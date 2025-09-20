<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\SubscriptionPlan;
use App\TargetUserType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('username')
                    ->searchable(),
                BadgeColumn::make('user_type')
                    ->formatStateUsing(fn (TargetUserType $state): string => $state->label())
                    ->colors([
                        'primary' => TargetUserType::Male,
                        'success' => TargetUserType::Female,
                        'warning' => TargetUserType::Couple,
                        'gray' => TargetUserType::Any,
                    ])
                    ->sortable(),
                BadgeColumn::make('subscription_plan')
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
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stripe_id')
                    ->searchable(),
                TextColumn::make('pm_type')
                    ->searchable(),
                TextColumn::make('pm_last_four')
                    ->searchable(),
                TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscription_ends_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('has_used_trial')
                    ->boolean(),
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
