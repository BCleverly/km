<?php

namespace App\Filament\Resources;

use App\Enums\SubscriptionPlan;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Plan Type')
                            ->options([
                                'free' => 'Free',
                                'monthly' => 'Monthly',
                                'couple' => 'Couple',
                                'lifetime' => 'Lifetime',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('stripe_id')
                            ->label('Stripe ID')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('stripe_status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'canceled' => 'Canceled',
                                'cancelled' => 'Cancelled',
                                'incomplete' => 'Incomplete',
                                'incomplete_expired' => 'Incomplete Expired',
                                'past_due' => 'Past Due',
                                'trialing' => 'Trialing',
                                'unpaid' => 'Unpaid',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('stripe_price')
                            ->label('Stripe Price ID')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1),

                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At'),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Ends At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Plan')
                    ->colors([
                        'secondary' => 'free',
                        'primary' => 'monthly',
                        'success' => 'couple',
                        'warning' => 'lifetime',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => 'Free',
                        'monthly' => 'Monthly',
                        'couple' => 'Couple',
                        'lifetime' => 'Lifetime',
                        default => ucfirst($state),
                    }),

                Tables\Columns\BadgeColumn::make('stripe_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => ['canceled', 'cancelled', 'incomplete_expired', 'unpaid'],
                        'warning' => ['incomplete', 'past_due', 'trialing'],
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'canceled' => 'Canceled',
                        'cancelled' => 'Cancelled',
                        'incomplete' => 'Incomplete',
                        'incomplete_expired' => 'Incomplete Expired',
                        'past_due' => 'Past Due',
                        'trialing' => 'Trialing',
                        'unpaid' => 'Unpaid',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plan Name')
                    ->getStateUsing(fn (Subscription $record): string => $record->plan_name),

                Tables\Columns\TextColumn::make('formatted_plan_price')
                    ->label('Price')
                    ->getStateUsing(fn (Subscription $record): string => $record->formatted_plan_price),

                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Ends At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Plan Type')
                    ->options([
                        'free' => 'Free',
                        'monthly' => 'Monthly',
                        'couple' => 'Couple',
                        'lifetime' => 'Lifetime',
                    ]),

                Tables\Filters\SelectFilter::make('stripe_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'canceled' => 'Canceled',
                        'cancelled' => 'Cancelled',
                        'incomplete' => 'Incomplete',
                        'incomplete_expired' => 'Incomplete Expired',
                        'past_due' => 'Past Due',
                        'trialing' => 'Trialing',
                        'unpaid' => 'Unpaid',
                    ]),

                Tables\Filters\Filter::make('trial_ending_soon')
                    ->label('Trial Ending Soon')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('trial_ends_at')
                        ->where('trial_ends_at', '<=', now()->addDays(3))
                        ->where('trial_ends_at', '>', now())),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn (Builder $query): Builder => $query->where('ends_at', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->latest();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('stripe_status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}