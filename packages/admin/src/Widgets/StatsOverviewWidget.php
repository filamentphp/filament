<?php

namespace Filament\Widgets;

class StatsOverviewWidget extends Widget
{
    protected static int $columns = 3;

    protected static string $view = 'filament::widgets.stats-overview-widget';

    protected function getColumns(): int
    {
        return static::$columns;
    }

    protected function getStats(): array
    {
        return [];
    }
}
