<?php

namespace App\Filament\Resources\BarterCategoryResource\Pages;

use App\Filament\Resources\BarterCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarterCategories extends ListRecords
{
    protected static string $resource = BarterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
