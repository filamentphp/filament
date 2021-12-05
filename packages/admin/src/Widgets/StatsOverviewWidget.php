<?php

namespace Filament\Widgets;

class StatsOverviewWidget extends Widget
{
    protected static string $view = 'filament::widgets.stats-overview-widget';

    protected function getColumns(): int
    {
        return match ($count = count($this->getCards())) {
            5, 6, 9, 11 => 3,
            7, 8, 10, 12 => 4,
            default => $count,
        };
    }

    protected function getCards(): array
    {
        return [];
    }
}
