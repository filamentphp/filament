<?php

namespace Filament\Widgets;

class StatsOverviewWidget extends Widget
{
    use Concerns\CanPoll;

    protected ?array $cachedCards = null;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament::widgets.stats-overview-widget';

    protected function getColumns(): int
    {
        return match ($count = count($this->getCachedCards())) {
            5, 6, 9, 11 => 3,
            7, 8, 10, 12 => 4,
            default => $count,
        };
    }

    protected function getCachedCards(): array
    {
        return $this->cachedCards ??= $this->getCards();
    }

    protected function getCards(): array
    {
        return [];
    }
}
