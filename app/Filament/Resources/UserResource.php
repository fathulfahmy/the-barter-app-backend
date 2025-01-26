<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserReportReason;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'user';

    protected static ?string $pluralModelLabel = 'users';

    protected static ?string $slug = 'users';

    protected static ?string $navigationGroup = 'General';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $activeNavigationIcon = 'heroicon-m-user-circle';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('user_avatar')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->fetchFileInformation(false)
                            ->visibility('public'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->rules(['string', 'max:255']),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules(['string', 'email', 'max:255']),
                        Forms\Components\Select::make('bank_name')
                            ->native(false)
                            ->options([
                                'Affin Bank',
                                'Agro Bank',
                                'Alliance Bank',
                                'Ambank',
                                'Bank Islam',
                                'Bank Muamalat',
                                'Bank Rakyat',
                                'Bank Simpanan Malaysia',
                                'CIMB Bank',
                                'Hong Leong Bank',
                                'HSBC Bank',
                                'Maybank',
                                'OCBC Bank',
                                'Public Bank',
                                'RHB Bank',
                                'Standard Chartered Bank',
                                'United Overseas Bank',
                            ])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules(['string', 'max:255']),
                        Forms\Components\TextInput::make('bank_account_number')
                            ->required()
                            ->rules(['string', 'max:255']),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->rules([Password::defaults()])
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->default(fn (?Model $record) => $record === null ? 'password' : null),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make('Suspend')
                    ->schema([
                        Forms\Components\Select::make('suspension_reason_id')
                            ->label('Reason')
                            ->options($reasons = UserReportReason::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->requiredWith('suspension_starts_at')
                            ->rules(['exists:user_report_reasons,id']),
                        Forms\Components\DateTimePicker::make('suspension_starts_at')
                            ->hiddenLabel()
                            ->prefix('Starts')
                            ->timezone('Asia/Kuala_Lumpur')
                            ->seconds(false)
                            ->native(false)
                            ->live()
                            ->requiredWith('suspension_reason_id')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('clear')
                                    ->color('gray')
                                    ->icon('heroicon-m-x-mark')
                                    ->action(fn (Set $set) => $set('suspension_starts_at', null))
                            )
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state > $get('suspension_ends_at')) {
                                    $set('suspension_ends_at', null);
                                }
                            }),
                        Forms\Components\DateTimePicker::make('suspension_ends_at')
                            ->hiddenLabel()
                            ->prefix('Ends')
                            ->timezone('Asia/Kuala_Lumpur')
                            ->seconds(false)
                            ->native(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('clear')
                                    ->color('gray')
                                    ->icon('heroicon-m-x-mark')
                                    ->action(fn (Set $set) => $set('suspension_ends_at', null))
                            )
                            ->minDate(fn (Get $get) => $get('suspension_starts_at'))
                            ->after('suspension_starts_at')
                            ->prohibitedUnless('suspension_starts_at', fn ($state) => $state !== null),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('user_avatar')
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bank_account_number')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('suspension_starts_at')
                    ->label('Suspension starts')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('suspension_ends_at')
                    ->label('Suspension ends')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('suspension_reason.name')
                    ->label('Suspension reason')
                    ->wrap()
                    ->sortable()
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
                Tables\Actions\SuspendAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\SuspendBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->isNotAdmin();
    }
}
