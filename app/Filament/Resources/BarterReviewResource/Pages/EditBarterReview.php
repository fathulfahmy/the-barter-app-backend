<?php

namespace App\Filament\Resources\BarterReviewResource\Pages;

use App\Filament\Resources\BarterReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarterReview extends EditRecord
{
    protected static string $resource = BarterReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
