<?php

namespace App\Filament\Resources\BarterTransactionResource\Pages;

use App\Filament\Resources\BarterTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarterTransaction extends EditRecord
{
    protected static string $resource = BarterTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
