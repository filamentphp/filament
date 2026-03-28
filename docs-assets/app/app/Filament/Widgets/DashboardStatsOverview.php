<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '$192,400')
                ->description('12% increase')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->chart([4120, 4832, 5221, 5694, 6012, 7340, 8210])
                ->color('success'),
            Stat::make('New customers', '1,340')
                ->description('3% decrease')
                ->descriptionIcon(Heroicon::ArrowTrendingDown)
                ->chart([3214, 3104, 2981, 3012, 2870, 2756, 2640])
                ->color('danger'),
            Stat::make('New orders', '3,543')
                ->description('7% increase')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->chart([2100, 2340, 2580, 2740, 2950, 3210, 3543])
                ->color('success'),
        ];
    }
}
