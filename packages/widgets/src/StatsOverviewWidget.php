<?php

namespace Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends Widget
{
    use Concerns\CanPoll;

    /**
     * @var array<Stat> | null
     */
    protected ?array $cachedStats = null;

    protected int | string | array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected static string $view = 'filament-widgets::stats-overview-widget';

    protected function getColumns(): int
    {
        $count = count($this->getCachedStats());

        if ($count < 3) {
            return 3;
        }

        if (($count % 3) !== 1) {
            return 3;
        }

        return 4;
    }

    /**
     * @return array<Stat>
     */
    protected function getCachedStats(): array
    {
        return $this->cachedStats ??= $this->getStats();
    }

    /**
     * @deprecated Use `getStats()` instead.
     *
     * @return array<Stat>
     */
    protected function getCards(): array
    {
        return [];
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return $this->getCards();
    }
}
