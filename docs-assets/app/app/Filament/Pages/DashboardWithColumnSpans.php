<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardOrdersChart;
use App\Filament\Widgets\DashboardRevenueChart;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\DashboardTableWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class DashboardWithColumnSpans extends BaseDashboard
{
    protected static string $routePath = 'dashboard-column-spans';

    protected static ?string $title = 'Dashboard';

    protected static bool $shouldRegisterNavigation = false;

    public function getColumns(): int | array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            DashboardRevenueChart::class,
            DashboardOrdersChart::class,
            DashboardTableWidget::class,
        ];
    }
}
