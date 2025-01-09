<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarterReviewResource\Pages;
use App\Models\BarterReview;
use App\Models\BarterTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarterReviewResource extends Resource
{
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $model = BarterReview::class;

    protected static ?string $modelLabel = 'review';

    protected static ?string $pluralModelLabel = 'reviews';

    protected static ?string $slug = 'reviews';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $activeNavigationIcon = 'heroicon-s-star';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('rating')
                            ->required()
                            ->default(0)
                            ->rules(['integer', 'min:0', 'max:5']),
                        Forms\Components\Textarea::make('description')
                            ->autosize()
                            ->required()
                            ->rules(['string', 'max:65535']),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('barter_transaction_id')
                            ->label('Transaction ID')
                            ->options(
                                BarterTransaction::query()
                                    ->where('status', 'completed')
                                    ->pluck('id', 'id')
                            )
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->rules(['exists:barter_transactions,id']),
                        Forms\Components\Select::make('author_id')
                            ->label('Author')
                            ->options(function (Get $get) {
                                $barter_transaction_id = $get('barter_transaction_id');

                                if (! $barter_transaction_id) {
                                    return ['' => 'No transaction selected'];
                                }

                                $barter_transaction = BarterTransaction::query()
                                    ->with(['barter_provider', 'barter_acquirer'])
                                    ->find($barter_transaction_id);

                                if (! $barter_transaction) {
                                    return [];
                                }

                                $options = [
                                    $barter_transaction->barter_acquirer_id => $barter_transaction->barter_acquirer->name,
                                    $barter_transaction->barter_provider_id => $barter_transaction->barter_provider->name,
                                ];

                                return $options;
                            })
                            ->native(false)
                            ->required()
                            ->rules(['exists:users,id']),
                    ])
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barter_transaction.id')
                    ->label('Transaction ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
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
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
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
            'index' => Pages\ListBarterReviews::route('/'),
            'create' => Pages\CreateBarterReview::route('/create'),
            'view' => Pages\ViewBarterReview::route('/{record}'),
            'edit' => Pages\EditBarterReview::route('/{record}/edit'),
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
