<?php

namespace App\Filament\Resources\UserReportReasonResource\Pages;

use App\Filament\Resources\UserReportReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserReportReasons extends ListRecords
{
    protected static string $resource = UserReportReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
