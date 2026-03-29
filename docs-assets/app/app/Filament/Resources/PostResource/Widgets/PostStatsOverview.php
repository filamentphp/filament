<?php

namespace App\Filament\Resources\PostResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total posts', '8')
                ->description('All time')
                ->chart([3, 5, 4, 7, 6, 8, 8]),
            Stat::make('Published', '6')
                ->description('75% published')
                ->color('success')
                ->chart([2, 3, 4, 5, 5, 6, 6]),
            Stat::make('Average rating', '7.6')
                ->description('Out of 10')
                ->color('warning')
                ->chart([6, 7, 8, 7, 8, 7, 8]),
        ];
    }
}
