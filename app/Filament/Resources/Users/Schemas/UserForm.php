<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\SubscriptionPlan;
use App\TargetUserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('username'),
                Select::make('user_type')
                    ->options(TargetUserType::class)
                    ->default(4)
                    ->required(),
                Select::make('subscription_plan')
                    ->options(SubscriptionPlan::class)
                    ->required()
                    ->default(0),
                Select::make('partner_id')
                    ->relationship('partner', 'name'),
                TextInput::make('stripe_id'),
                TextInput::make('pm_type'),
                TextInput::make('pm_last_four'),
                DateTimePicker::make('trial_ends_at'),
                DateTimePicker::make('subscription_ends_at'),
                Toggle::make('has_used_trial')
                    ->required(),
            ]);
    }
}
