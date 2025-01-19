<?php

namespace App\Filament\Resources\UserReportResource\Pages;

use App\Filament\Resources\UserReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserReports extends ListRecords
{
    protected static string $resource = UserReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
