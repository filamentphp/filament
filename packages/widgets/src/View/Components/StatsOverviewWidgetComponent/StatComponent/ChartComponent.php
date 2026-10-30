<?php

namespace Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent;

use Filament\Support\View\Components\Contracts\HasColorMap;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class ChartComponent implements HasColorMap, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        return [];
    }
}
