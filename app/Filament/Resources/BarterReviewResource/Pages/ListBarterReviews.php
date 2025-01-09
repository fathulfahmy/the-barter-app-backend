<?php

namespace App\Filament\Resources\BarterReviewResource\Pages;

use App\Filament\Resources\BarterReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarterReviews extends ListRecords
{
    protected static string $resource = BarterReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
