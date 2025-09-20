<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = Heroicon::OutlineCreditCard;

    protected static ?string $navigationLabel = 'Subscriptions';

    protected static ?string $modelLabel = 'Subscription';

    protected static ?string $pluralModelLabel = 'Subscriptions';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('subscription_plan')
                    ->options([
                        'free' => 'Free',
                        'solo' => 'Solo',
                        'premium' => 'Premium',
                        'couple' => 'Couple',
                        'lifetime' => 'Lifetime',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('subscription_ends_at')
                    ->label('Subscription Ends At'),
                Forms\Components\DateTimePicker::make('trial_ends_at')
                    ->label('Trial Ends At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::query()->whereHas('subscriptions'))
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'gray',
                        'solo' => 'blue',
                        'premium' => 'green',
                        'couple' => 'purple',
                        'lifetime' => 'gold',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subscriptions.stripe_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'past_due' => 'warning',
                        'canceled' => 'danger',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->label('Ends At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_plan')
                    ->options([
                        'free' => 'Free',
                        'solo' => 'Solo',
                        'premium' => 'Premium',
                        'couple' => 'Couple',
                        'lifetime' => 'Lifetime',
                    ]),
                Tables\Filters\SelectFilter::make('stripe_status')
                    ->options([
                        'active' => 'Active',
                        'past_due' => 'Past Due',
                        'canceled' => 'Canceled',
                        'unpaid' => 'Unpaid',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => ListSubscriptions::route('/'),
        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->username . ' (' . $record->email . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Plan' => $record->subscription_plan->label(),
            'Status' => $record->subscriptions->first()?->stripe_status ?? 'No subscription',
            'Ends At' => $record->subscription_ends_at?->format('M j, Y') ?? 'N/A',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['username', 'email'];
    }
}
