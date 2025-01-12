<?php

namespace Filament\Tables\Actions;

use App\Models\User as Model;
use App\Models\UserReportReason;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Collection;

class SuspendBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'suspend-bulk';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Suspend selected');

        $this->modalHeading(fn (): string => 'Suspend '.$this->getPluralModelLabel());

        $this->modalSubmitActionLabel('Suspend');

        $this->successNotificationTitle('Saved');

        $this->color('danger');

        $this->icon('heroicon-m-hand-raised');

        $reasons = UserReportReason::pluck('name')->toArray();

        $this->form([
            Radio::make('action')
                ->options([
                    'suspend_temporarily' => 'Suspend temporarily',
                    'suspend_permanently' => 'Suspend permanently',
                    'unsuspend' => 'Unsuspend',
                ])
                ->required()
                ->live(),
            Select::make('suspension_reason_id')
                ->label('Reason')
                ->options($reasons = UserReportReason::pluck('name', 'id')->toArray())
                ->default(array_key_first($reasons))
                ->native(false)
                ->searchable()
                ->required()
                ->rules(['exists:user_report_reasons,id'])
                ->visible(
                    fn (Get $get) => $get('action') === 'suspend_temporarily' ||
                    $get('action') === 'suspend_permanently'
                ),
            DateTimePicker::make('suspension_starts_at')
                ->hiddenLabel()
                ->prefix('Starts')
                ->timezone('Asia/Kuala_Lumpur')
                ->seconds(false)
                ->native(false)
                ->live()
                ->required()
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    if ($state > $get('suspension_ends_at')) {
                        $set('suspension_ends_at', null);
                    }
                })
                ->suffixAction(
                    FormAction::make('clear')
                        ->color('gray')
                        ->icon('heroicon-m-x-mark')
                        ->action(fn (Set $set) => $set('suspension_starts_at', null))
                )
                ->visible(
                    fn (Get $get) => $get('action') === 'suspend_temporarily' ||
                    $get('action') === 'suspend_permanently'
                ),
            DateTimePicker::make('suspension_ends_at')
                ->hiddenLabel()
                ->prefix('Ends')
                ->timezone('Asia/Kuala_Lumpur')
                ->seconds(false)
                ->native(false)
                ->required()
                ->minDate(fn (Get $get) => $get('suspension_starts_at'))
                ->after('suspension_starts_at')
                ->suffixAction(
                    FormAction::make('clear')
                        ->color('gray')
                        ->icon('heroicon-m-x-mark')
                        ->action(fn (Set $set) => $set('suspension_ends_at', null))
                )
                ->visible(
                    fn (Get $get) => $get('action') === 'suspend_temporarily'
                ),
        ]);

        $this->fillForm(function (Model $record) use ($reasons): array {
            $data = [
                'action' => 'suspend_temporarily',
                'suspension_reason_id' => array_key_first($reasons),
                'suspension_starts_at' => now(),
                'suspension_ends_at' => now()->addDays(14),
            ];

            return $data;
        });

        $this->action(function (): void {
            $this->process(static function (array $data, Collection $records): void {
                if ($data['action'] === 'unsuspend') {

                    $records->each(function (Model $record) {
                        $record->unsuspend();
                    });

                } else {
                    $records->each(function (Model $record) use ($data) {
                        $record->suspend(
                            $data['suspension_reason_id'],
                            $data['suspension_starts_at'],
                            $data['suspension_ends_at'] ?? null
                        );
                    });
                }
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
