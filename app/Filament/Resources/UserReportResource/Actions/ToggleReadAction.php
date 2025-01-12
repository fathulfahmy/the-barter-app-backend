<?php

namespace Filament\Tables\Actions;

use App\Models\UserReport as Model;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;

class ToggleReadAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'toggle-read';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(
            fn (Model $record): string => $record->status === 'read' ? 'Mark as unread' : 'Mark as read'
        );

        $this->color('gray');

        $this->icon(
            fn (Model $record): string => $record->status === 'read' ? 'heroicon-m-envelope' : 'heroicon-m-envelope-open'
        );

        $this->action(function (): void {
            $this->process(function (array $data, Model $record) {
                $status = $record->status === 'read' ? 'unread' : 'read';
                $record->update(['status' => $status]);
            });

            $this->success();
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
