<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserReportResource\Pages;
use App\Models\UserReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserReportResource extends Resource
{
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $model = UserReport::class;

    protected static ?string $modelLabel = 'report';

    protected static ?string $pluralModelLabel = 'reports';

    protected static ?string $slug = 'reports';

    protected static ?string $navigationGroup = 'Report';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $activeNavigationIcon = 'heroicon-m-flag';

    protected static ?int $navigationSort = 0;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'unread')->count();
    }

    protected static ?string $navigationBadgeTooltip = 'Unread reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('model_id')
                            ->label('Model ID')
                            ->required()
                            ->numeric()
                            ->rules(['integer', 'min:0']),
                        Forms\Components\Select::make('model_name')
                            ->label('Model')
                            ->options([
                                'user' => 'User',
                                'barter_service' => 'Barter Service',
                                'barter_transaction' => 'Barter Transaction',
                                'barter_review' => 'Barter Review',
                            ])
                            ->native(false)
                            ->required()
                            ->rules(['string', 'max:255']),
                        Forms\Components\Select::make('user_report_reason_id')
                            ->label('Reason')
                            ->relationship('user_report_reason', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name', fn (Builder $query) => $query->isNotAdmin())
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'read' => 'Read',
                                'unread' => 'Unread',
                            ])
                            ->default('unread')
                            ->native(false)
                            ->required(),
                    ])
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('model_id')
                    ->label('Model ID')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model_name')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => Str::of($state)->replace('_', ' ')->title())
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user_report_reason.name')
                    ->label('Reason')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Str::of($state)->replace('_', ' ')->title())
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'gray',
                        'read' => 'primary',
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ToggleReadAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ReadBulkAction::make(),
                    Tables\Actions\UnreadBulkAction::make(),
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
            'index' => Pages\ListUserReports::route('/'),
            'create' => Pages\CreateUserReport::route('/create'),
            'edit' => Pages\EditUserReport::route('/{record}/edit'),
        ];
    }
}
