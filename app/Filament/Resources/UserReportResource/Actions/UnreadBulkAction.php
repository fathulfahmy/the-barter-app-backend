<?php

namespace Filament\Tables\Actions;

use App\Models\UserReport as Model;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;

class UnreadBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'unread-bulk';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Mark as unread');

        $this->color('gray');

        $this->icon('heroicon-m-envelope');

        $this->action(function (): void {
            $this->process(static function (array $data, Collection $records): void {
                $records->each(function (Model $record) {
                    $record->update(['status' => 'unread']);
                });
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
