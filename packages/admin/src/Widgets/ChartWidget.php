<?php

namespace Filament\Widgets;

class ChartWidget extends Widget
{
    use Concerns\CanPoll;

    protected ?array $cachedData = null;

    public string $dataChecksum;

    public ?string $filter = null;

    protected static ?string $heading = null;

    protected static ?array $options = null;

    protected static string $view = 'filament::widgets.chart-widget';

    public function mount()
    {
        $this->dataChecksum = $this->generateDataChecksum();
    }

    protected function generateDataChecksum(): string
    {
        return md5(json_encode($this->getCachedData()));
    }

    protected function getCachedData(): array
    {
        return $this->cachedData ??= $this->getData();
    }

    protected function getData(): array
    {
        return [];
    }

    protected function getFilters(): ?array
    {
        return null;
    }

    protected function getHeading(): ?string
    {
        return static::$heading;
    }

    protected function getOptions(): ?array
    {
        return static::$options;
    }

    public function updateChartData()
    {
        $newDataChecksum = $this->generateDataChecksum();

        if ($newDataChecksum !== $this->dataChecksum) {
            $this->dataChecksum = $newDataChecksum;

            $this->emitSelf('updateChartData', [
                'data' => $this->getCachedData(),
            ]);
        }
    }

    public function updatedFilter(): void
    {
        $newDataChecksum = $this->generateDataChecksum();

        if ($newDataChecksum !== $this->dataChecksum) {
            $this->dataChecksum = $newDataChecksum;

            $this->emitSelf('filterChartData', [
                'data' => $this->getCachedData(),
            ]);
        }
    }
}
