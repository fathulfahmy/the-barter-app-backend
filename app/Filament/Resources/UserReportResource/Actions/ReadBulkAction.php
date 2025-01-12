<?php

namespace Filament\Tables\Actions;

use App\Models\UserReport as Model;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;

class ReadBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'read-bulk';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Mark as read');

        $this->color('gray');

        $this->icon('heroicon-m-envelope-open');

        $this->action(function (): void {
            $this->process(static function (array $data, Collection $records): void {
                $records->each(function (Model $record) {
                    $record->update(['status' => 'read']);
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
