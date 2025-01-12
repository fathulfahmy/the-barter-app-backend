<?php

namespace App\Filament\Widgets;

use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
use App\Models\UserReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unread reports', UserReport::where('status', 'unread')->count())
                ->color('danger'),
            Stat::make('Users', User::isNotAdmin()->count()),
            Stat::make('Services', BarterService::count()),
            Stat::make('Transactions', BarterTransaction::count()),
        ];
    }
}
