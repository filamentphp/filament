<?php

namespace Filament\Widgets\StatsOverviewWidget\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Livewire\Attributes\Locked;

trait HasChartData
{
    /**
     * @var array<string, string>
     */
    #[Locked]
    public array $chartDataChecksums = [];

    #[Unexposed]
    public function mountHasChartData(): void
    {
        $this->chartDataChecksums = $this->getStatChartDataChecksums();
    }

    #[Unexposed]
    public function renderingHasChartData(): void
    {
        $this->updateChartData();
    }

    #[Unexposed]
    public function updateChartData(): void
    {
        foreach ($this->getCachedStats() as $stat) {
            if ($stat->getChart() === null) {
                continue;
            }

            $key = $stat->getKey(isAbsolute: false);

            if ($key === null) {
                continue;
            }

            $newChecksum = $stat->generateChartDataChecksum();

            if (($this->chartDataChecksums[$key] ?? null) === $newChecksum) {
                continue;
            }

            $this->chartDataChecksums[$key] = $newChecksum;

            $this->dispatch('updateStatsOverviewChartData', key: $key, data: array_values($stat->getChart()));
        }
    }

    /**
     * @return array<string, string>
     */
    protected function getStatChartDataChecksums(): array
    {
        $checksums = [];

        foreach ($this->getCachedStats() as $stat) {
            if ($stat->getChart() === null) {
                continue;
            }

            $key = $stat->getKey(isAbsolute: false);

            if ($key === null) {
                continue;
            }

            $checksums[$key] = $stat->generateChartDataChecksum();
        }

        return $checksums;
    }
}
