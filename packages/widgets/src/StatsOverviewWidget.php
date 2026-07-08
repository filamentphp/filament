<?php

namespace Filament\Widgets;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Locked;

class StatsOverviewWidget extends Widget implements HasSchemas
{
    use Concerns\CanPoll;
    use InteractsWithSchemas;

    /**
     * @var array<Stat> | null
     */
    protected ?array $cachedStats = null;

    /**
     * @var array<string, string>
     */
    #[Locked]
    public array $chartDataChecksums = [];

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = null;

    protected ?string $description = null;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int | array | null $columns = null;

    /**
     * @var view-string
     */
    protected string $view = 'filament-widgets::stats-overview-widget';

    public function mount(): void
    {
        $this->chartDataChecksums = $this->getStatChartDataChecksums();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getSectionContentComponent(),
            ]);
    }

    public function rendering(): void
    {
        $this->updateChartData();
    }

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

    public function getSectionContentComponent(): Component
    {
        return Section::make()
            ->heading($this->getHeading())
            ->description($this->getDescription())
            ->schema($this->getCachedStats())
            ->columns($this->getColumns())
            ->contained(false)
            ->gridContainer();
    }

    /**
     * @return int | array<string, ?int> | null
     */
    protected function getColumns(): int | array | null
    {
        if ($this->columns) {
            return $this->columns;
        }

        $count = count($this->getCachedStats());

        if ($count < 3) {
            return ['@xl' => 3, '!@lg' => 3];
        }

        if (($count % 3) !== 1) {
            return ['@xl' => 3, '!@lg' => 3];
        }

        return ['@xl' => 4, '!@lg' => 4];
    }

    protected function getDescription(): ?string
    {
        return $this->description;
    }

    protected function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * @return array<Stat>
     */
    protected function getCachedStats(): array
    {
        if ($this->cachedStats !== null) {
            return $this->cachedStats;
        }

        $stats = array_values($this->getStats());

        foreach ($stats as $index => $stat) {
            if (($stat->getChart() !== null) && blank($stat->getKey(isAbsolute: false))) {
                $stat->key('stats-overview-stat-' . $index);
            }
        }

        return $this->cachedStats = $stats;
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
