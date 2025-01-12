<?php

namespace App\Filament\Resources\UserReportResource\Pages;

use App\Filament\Resources\UserReportResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUserReports extends ListRecords
{
    protected static string $resource = UserReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'unread' => Tab::make('Unread')->modifyQueryUsing(
                fn (Builder $query) => $query->where('status', 'unread'),
            ),
            'read' => Tab::make('Read')->modifyQueryUsing(
                fn (Builder $query) => $query->where('status', 'read'),
            ),
        ];
    }
}
