<?php

namespace Filament\Widgets\StatsOverviewWidget;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanOpenUrl;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;

class Stat extends Component
{
    use CanOpenUrl;
    use HasColor;
    use HasDescription;
    use HasLabel;

    protected string $view = 'filament-widgets::stats-overview-widget.stat';

    /**
     * @var array<float> | null
     */
    protected ?array $chart = null;

    /**
     * @var string | array<string> | null
     */
    protected string | array | null $chartColor = null;

    protected string | BackedEnum | null $icon = null;

    protected string | BackedEnum | null $descriptionIcon = null;

    protected IconPosition | string | null $descriptionIconPosition = null;

    /**
     * @var string | array<string> | null
     */
    protected string | array | null $descriptionColor = null;

    /**
     * @var scalar | Htmlable | Closure
     */
    protected $value;

    /**
     * @param  scalar | Htmlable | Closure  $value
     */
    final public function __construct(string | Htmlable $label, $value)
    {
        $this->label($label);
        $this->value($value);
    }

    /**
     * @param  scalar | Htmlable | Closure  $value
     */
    public static function make(string | Htmlable $label, $value): static
    {
        return app(static::class, ['label' => $label, 'value' => $value]);
    }

    /**
     * @param  string | array<string> | null  $color
     */
    public function chartColor(string | array | null $color): static
    {
        $this->chartColor = $color;

        return $this;
    }

    public function icon(string | BackedEnum | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  string | array<string> | null  $color
     */
    public function descriptionColor(string | array | null $color): static
    {
        $this->descriptionColor = $color;

        return $this;
    }

    public function descriptionIcon(string | BackedEnum | null $icon, IconPosition | string | null $position = null): static
    {
        $this->descriptionIcon = $icon;
        $this->descriptionIconPosition = $position;

        return $this;
    }

    /**
     * @param  array<float> | Arrayable | null  $chart
     */
    public function chart(array | Arrayable | null $chart): static
    {
        if (is_null($chart)) {
            return $this;
        }

        if ($chart instanceof Arrayable) {
            $chart = $chart->toArray();
        }

        $this->chart = $chart;

        return $this;
    }

    /**
     * @param  scalar | Htmlable | Closure  $value
     */
    public function value($value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array<float> | null
     */
    public function getChart(): ?array
    {
        return $this->chart;
    }

    /**
     * @return string | array<string> | null
     */
    public function getChartColor(): string | array | null
    {
        return $this->chartColor ?? $this->getColor();
    }

    public function getIcon(): string | BackedEnum | null
    {
        return $this->icon;
    }

    /**
     * @return string | array<string> | null
     */
    public function getDescriptionColor(): string | array | null
    {
        return $this->descriptionColor ?? $this->getColor();
    }

    public function getDescriptionIcon(): string | BackedEnum | null
    {
        return $this->descriptionIcon;
    }

    public function getDescriptionIconPosition(): IconPosition | string
    {
        return $this->descriptionIconPosition ?? IconPosition::After;
    }

    /**
     * @return scalar | Htmlable | Closure
     */
    public function getValue(): mixed
    {
        return value($this->value);
    }

    public function generateChartDataChecksum(): string
    {
        return md5(json_encode($this->getChart()) . now());
    }
}
