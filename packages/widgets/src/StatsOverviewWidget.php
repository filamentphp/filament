<?php

namespace Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends Widget
{
    use Concerns\CanPoll;

    /**
     * @var array<Card> | null
     */
    protected ?array $cachedCards = null;

    protected int | string | array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected static string $view = 'filament-widgets::stats-overview-widget';

    protected function getColumns(): int
    {
        $count = count($this->getCachedCards());

        if ($count < 3) {
            return 3;
        }

        if (($count % 3) !== 1) {
            return 3;
        }

        return 4;
    }

    /**
     * @return array<Card>
     */
    protected function getCachedCards(): array
    {
        return $this->cachedCards ??= $this->getCards();
    }

    /**
     * @return array<Card>
     */
    protected function getCards(): array
    {
        return [];
    }
}
