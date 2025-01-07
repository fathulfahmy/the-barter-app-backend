<?php

namespace App\Filament\Resources\BarterServiceResource\Pages;

use App\Filament\Resources\BarterServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarterServices extends ListRecords
{
    protected static string $resource = BarterServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
