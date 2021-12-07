<?php

namespace Filament\Widgets;

class ChartWidget extends Widget
{
    public string $dataChecksum;

    protected static ?string $heading = null;

    protected static ?array $options = null;

    protected static ?string $pollingInterval = '5s';

    protected static string $view = 'filament::widgets.chart-widget';

    public function mount()
    {
        $this->dataChecksum = $this->generateDataChecksum();
    }

    protected function generateDataChecksum(): string
    {
        return md5(json_encode($this->getData()));
    }

    protected function getData(): array
    {
        return [];
    }

    protected function getHeading(): ?string
    {
        return static::$heading;
    }

    protected function getOptions(): ?array
    {
        return static::$options;
    }

    protected function getPollingInterval(): ?string
    {
        return static::$pollingInterval;
    }

    public function updateChartData()
    {
        $newDataChecksum = $this->generateDataChecksum();

        if ($newDataChecksum !== $this->dataChecksum) {
            $this->dataChecksum = $newDataChecksum;

            $this->emitSelf('updateChartData', [
                'data' => $this->getData(),
            ]);
        }
    }
}
