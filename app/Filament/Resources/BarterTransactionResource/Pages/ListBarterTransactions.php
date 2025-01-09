<?php

namespace App\Filament\Resources\BarterTransactionResource\Pages;

use App\Filament\Resources\BarterTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarterTransactions extends ListRecords
{
    protected static string $resource = BarterTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
