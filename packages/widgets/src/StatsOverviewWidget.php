<?php

namespace Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends Widget
{
    use Concerns\CanPoll;

    /**
     * @var array<Card> | null $cachedCards
     */
    protected ?array $cachedCards = null;

    protected int | string | array $columnSpan = 'full';

    /**
     * @var view-string $view
     */
    protected static string $view = 'filament-widgets::stats-overview-widget';

    protected function getColumns(): int
    {
        return match ($count = count($this->getCachedCards())) {
            5, 6, 9, 11 => 3,
            7, 8, 10, 12 => 4,
            default => $count,
        };
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
