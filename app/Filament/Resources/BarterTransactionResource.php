<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarterTransactionResource\Pages;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BarterTransactionResource extends Resource
{
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $model = BarterTransaction::class;

    protected static ?string $modelLabel = 'transaction';

    protected static ?string $pluralModelLabel = 'transactions';

    protected static ?string $navigationGroup = 'Barters';

    protected static ?string $slug = 'transactions';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $activeNavigationIcon = 'heroicon-m-currency-dollar';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('barter_acquirer_id')
                            ->label('Acquirer')
                            ->options(
                                function (Get $get) {
                                    $barter_provider_id = $get('barter_provider_id');

                                    $query = User::query()
                                        ->isNotAdmin()
                                        ->when($barter_provider_id, function ($query) use ($barter_provider_id) {
                                            return $query->whereNot('id', $barter_provider_id);
                                        });

                                    return $query->pluck('name', 'id');
                                },
                            )
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('barter_invoice.barter_acquirer_id', $state);
                                $set('barter_invoice.barter_services', []);
                            })
                            ->native(false)
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules(['exists:users,id']),
                        Forms\Components\Section::make()
                            ->relationship('barter_invoice')
                            ->schema([
                                Forms\Components\Hidden::make('barter_acquirer_id'),
                                Forms\Components\TextInput::make('amount')
                                    ->prefix('RM')
                                    ->numeric()
                                    ->default(0.00)
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0.00)
                                    ->rules(['min:0']),
                                Forms\Components\CheckboxList::make('barter_services')
                                    ->relationship()
                                    ->label('Services')
                                    ->options(function (Get $get) {
                                        $barter_acquirer_id = $get('../barter_acquirer_id');

                                        if (! $barter_acquirer_id) {
                                            return ['' => 'No acquirer selected'];
                                        }

                                        return BarterService::query()
                                            ->where('barter_provider_id', $barter_acquirer_id)
                                            ->pluck('title', 'id');
                                    })
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->nullable(),
                            ]),
                    ])
                    ->columnSpan(1),
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('id')
                                    ->label('Transaction ID')
                                    ->visibleOn(['view', 'edit'])
                                    ->readOnly(fn ($context) => in_array($context, ['edit', 'view'])),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'accepted' => 'Accepted',
                                        'rejected' => 'Rejected',
                                        'awaiting_completed' => 'Awaiting Completed',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if ($state !== 'awaiting_completed') {
                                            $set('awaiting_user_id', null);
                                        }
                                    })
                                    ->required(),
                                Forms\Components\Select::make('awaiting_user_id')
                                    ->label('Awaiting user')
                                    ->options(function (Get $get) {
                                        $barter_acquirer_id = $get('barter_acquirer_id');
                                        $barter_provider_id = $get('barter_provider_id');

                                        if (! $barter_acquirer_id || ! $barter_provider_id) {
                                            return ['' => 'No acquirer or provider selected'];
                                        }

                                        return User::query()
                                            ->whereIn('id', [$barter_acquirer_id, $barter_provider_id])
                                            ->pluck('name', 'id');
                                    })
                                    ->visible(function (Get $get) {
                                        $status = $get('status');

                                        return $status === 'awaiting_completed';
                                    })
                                    ->native(false)
                                    ->required()
                                    ->rules(['exists:users,id']),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('barter_provider_id')
                                    ->label('Provider')
                                    ->options(
                                        function (Builder $query, Get $get) {
                                            $barter_acquirer_id = $get('barter_acquirer_id');

                                            $query = User::query()
                                                ->isNotAdmin()
                                                ->when($barter_acquirer_id, function ($query) use ($barter_acquirer_id) {
                                                    return $query->whereNot('id', $barter_acquirer_id);
                                                });

                                            return $query->pluck('name', 'id');
                                        },
                                    )
                                    ->native(false)
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('barter_service_id')
                                    ->label('Service')
                                    ->options(function (Get $get) {
                                        $barter_provider_id = $get('barter_provider_id');

                                        if (! $barter_provider_id) {
                                            return ['' => 'No provider selected'];
                                        }

                                        return BarterService::query()
                                            ->where('barter_provider_id', $barter_provider_id)
                                            ->pluck('title', 'id');
                                    })
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('barter_acquirer.name')
                    ->label('Acquirer')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_invoice.amount')
                    ->label('Amount')
                    ->money('myr')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_invoice.barter_services.title')
                    ->label('Services')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_provider.name')
                    ->label('Provider')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_service.title')
                    ->label('Service')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Str::of($state)->replace('_', ' ')->title())
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'awaiting_completed' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('awaiting_user.name')
                    ->label('Awaiting user')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('pending')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'pending')),
                Tables\Filters\Filter::make('cancelled')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'cancelled')),
                Tables\Filters\Filter::make('accepted')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'accepted')),
                Tables\Filters\Filter::make('rejected')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'rejected')),
                Tables\Filters\Filter::make('awaiting_completed')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'awaiting_completed')),
                Tables\Filters\Filter::make('completed')
                    ->query(fn (Builder $query): Builder => $query->orWhere('status', 'completed')),
                Tables\Filters\TrashedFilter::make()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListBarterTransactions::route('/'),
            'create' => Pages\CreateBarterTransaction::route('/create'),
            'view' => Pages\ViewBarterTransaction::route('/{record}'),
            'edit' => Pages\EditBarterTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
