<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarterServiceResource\Pages;
use App\Models\BarterService;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BarterServiceResource extends Resource
{
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $model = BarterService::class;

    protected static ?string $modelLabel = 'service';

    protected static ?string $pluralModelLabel = 'services';

    protected static ?string $slug = 'services';

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $activeNavigationIcon = 'heroicon-s-briefcase';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->rules(['string', 'max:255']),
                        Forms\Components\Textarea::make('description')
                            ->autosize()
                            ->columnSpanFull()
                            ->required()
                            ->rules(['string', 'max:65535']),
                        Forms\Components\Fieldset::make()
                            ->label('Price range')
                            ->schema([
                                Forms\Components\TextInput::make('min_price')
                                    ->prefix('RM')
                                    ->numeric()
                                    ->default(0.00)
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0.00)
                                    ->required()
                                    ->rules([
                                        'min:0',
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if ($get('max_price') >= 0 && $value > $get('max_price')) {
                                                $fail('The min price field must be less than or equal to max price.');
                                            }
                                        },
                                    ]),
                                Forms\Components\TextInput::make('max_price')
                                    ->prefix('RM')
                                    ->numeric()
                                    ->default(0.00)
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0.00)
                                    ->required()
                                    ->rules([
                                        'min:0',
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if ($get('min_price') >= 0 && $value < $get('min_price')) {
                                                $fail('The max price field must be more than or equal to min price.');
                                            }
                                        },
                                    ]),
                                Forms\Components\TextInput::make('price_unit')
                                    ->required()
                                    ->rules(['string', 'max:255']),
                            ])
                            ->columns(3),
                    ])
                    ->columnspan(1),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('barter_provider_id')
                            ->label('Provider')
                            ->relationship('barter_provider', 'name', fn (Builder $query) => $query->isNotAdmin())
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules(['exists:users,id']),
                        Forms\Components\Select::make('barter_category_id')
                            ->label('Category')
                            ->relationship('barter_category', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules(['exists:barter_categories,id']),
                        Forms\Components\Select::make('status')
                            ->options([
                                'enabled' => 'Enabled',
                                'disabled' => 'Disabled',
                            ])
                            ->default('enabled')
                            ->native(false)
                            ->required(),
                    ])
                    ->columnspan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_price')
                    ->money('myr')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->money('myr')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price_unit')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_provider.name')
                    ->label('Provider')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barter_category.name')
                    ->label('Category')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Str::of($state)->replace('_', ' ')->title())
                    ->color(fn (string $state): string => match ($state) {
                        'enabled' => 'primary',
                        'disabled' => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListBarterServices::route('/'),
            'create' => Pages\CreateBarterService::route('/create'),
            'view' => Pages\ViewBarterService::route('/{record}'),
            'edit' => Pages\EditBarterService::route('/{record}/edit'),
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
