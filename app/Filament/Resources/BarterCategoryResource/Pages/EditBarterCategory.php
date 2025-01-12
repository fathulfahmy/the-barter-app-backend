<?php

namespace App\Filament\Resources\BarterCategoryResource\Pages;

use App\Filament\Resources\BarterCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarterCategory extends EditRecord
{
    protected static string $resource = BarterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
