<?php

namespace Filament\Widgets;

use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;

abstract class ChartWidget extends Widget
{
    use Concerns\CanPoll;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $cachedData = null;

    public string $dataChecksum;

    public ?string $filter = null;

    protected static string $color = 'primary';

    protected static ?string $heading = null;

    protected static ?string $description = null;

    protected static ?string $maxHeight = null;

    /**
     * @var array<string, mixed> | null
     */
    protected static ?array $options = null;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-widgets::chart-widget';

    public function mount(): void
    {
        $this->dataChecksum = $this->generateDataChecksum();
    }

    abstract protected function getType(): string;

    protected function generateDataChecksum(): string
    {
        return md5(json_encode($this->getCachedData()));
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCachedData(): array
    {
        return $this->cachedData ??= $this->getData();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return [];
    }

    /**
     * @return array<scalar, scalar> | null
     */
    protected function getFilters(): ?array
    {
        return null;
    }

    public function getHeading(): string | Htmlable | null
    {
        return static::$heading;
    }

    public function getDescription(): string | Htmlable | null
    {
        return static::$description;
    }

    protected function getMaxHeight(): ?string
    {
        return static::$maxHeight;
    }

    /**
     * @return array<string, mixed> | RawJs | null
     */
    protected function getOptions(): array | RawJs | null
    {
        return static::$options;
    }

    public function updateChartData(): void
    {
        $newDataChecksum = $this->generateDataChecksum();

        if ($newDataChecksum !== $this->dataChecksum) {
            $this->dataChecksum = $newDataChecksum;

            $this->dispatch('updateChartData', data: $this->getCachedData());
        }
    }

    public function updatedFilter(): void
    {
        $this->updateChartData();
    }

    public function getColor(): string
    {
        return static::$color;
    }
}
