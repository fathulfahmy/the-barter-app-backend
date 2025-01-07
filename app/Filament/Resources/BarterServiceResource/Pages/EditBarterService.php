<?php

namespace App\Filament\Resources\BarterServiceResource\Pages;

use App\Filament\Resources\BarterServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarterService extends EditRecord
{
    protected static string $resource = BarterServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
