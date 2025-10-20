<?php

namespace Filament\Widgets;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Locked;

abstract class ChartWidget extends Widget implements HasSchemas
{
    use Concerns\CanPoll;
    use InteractsWithSchemas;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $cachedData = null;

    #[Locked]
    public ?string $dataChecksum = null;

    public ?string $filter = null;

    protected string $color = 'primary';

    protected ?string $heading = null;

    protected ?string $description = null;

    protected ?string $maxHeight = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $options = null;

    protected bool $isCollapsible = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament-widgets::chart-widget';

    public function mount(): void
    {
        if (method_exists($this, 'getFiltersSchema')) {
            $this->getFiltersSchema()->fill();
        }

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
        return $this->heading;
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->description;
    }

    protected function getMaxHeight(): ?string
    {
        return $this->maxHeight;
    }

    /**
     * @return array<string, mixed> | RawJs | null
     */
    protected function getOptions(): array | RawJs | null
    {
        return $this->options;
    }

    public function updateChartData(): void
    {
        $newDataChecksum = $this->generateDataChecksum();

        if ($newDataChecksum !== $this->dataChecksum) {
            $this->dataChecksum = $newDataChecksum;

            $this->dispatch('updateChartData', data: $this->getCachedData());
        }
    }

    public function rendering(): void
    {
        $this->updateChartData();
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isCollapsible(): bool
    {
        return $this->isCollapsible;
    }
}
