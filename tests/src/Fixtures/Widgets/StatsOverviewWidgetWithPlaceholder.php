<?php

namespace Filament\Tests\Fixtures\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidgetWithPlaceholder extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total orders', 0),
            Stat::make('Conversion rate', null)
                ->placeholder('Not available'),
        ];
    }
}
