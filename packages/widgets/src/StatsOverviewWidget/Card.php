<?php

namespace Filament\Widgets\StatsOverviewWidget;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class Card extends Component implements Htmlable
{
    /**
     * @var array<string, mixed> | null
     */
    protected ?array $chart = null;

    protected ?string $chartColor = null;

    protected ?string $color = null;

    protected ?string $icon = null;

    protected string | Htmlable | null $description = null;

    protected ?string $descriptionIcon = null;

    protected ?string $descriptionIconPosition = null;

    protected ?string $descriptionColor = null;

    /**
     * @var array<string, scalar>
     */
    protected array $extraAttributes = [];

    protected bool $shouldOpenUrlInNewTab = false;

    protected ?string $url = null;

    protected ?string $id = null;

    protected string | Htmlable $label;

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

    public function chartColor(?string $color): static
    {
        $this->chartColor = $color;

        return $this;
    }

    public function color(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function description(string | Htmlable | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function descriptionColor(?string $color): static
    {
        $this->descriptionColor = $color;

        return $this;
    }

    public function descriptionIcon(?string $icon, ?string $position = 'after'): static
    {
        $this->descriptionIcon = $icon;
        $this->descriptionIconPosition = $position;

        return $this;
    }

    /**
     * @param  array<string, scalar>  $attributes
     */
    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function openUrlInNewTab(bool $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function url(?string $url, bool $shouldOpenInNewTab = false): static
    {
        $this->shouldOpenUrlInNewTab = $shouldOpenInNewTab;
        $this->url = $url;

        return $this;
    }

    /**
     * @param  array<string, mixed> | null  $chart
     */
    public function chart(?array $chart): static
    {
        $this->chart = $chart;

        return $this;
    }

    public function label(string | Htmlable $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;

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
     * @return array<string, mixed> | null
     */
    public function getChart(): ?array
    {
        return $this->chart;
    }

    public function getChartColor(): ?string
    {
        return $this->chartColor ?? $this->color;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->description;
    }

    public function getDescriptionColor(): ?string
    {
        return $this->descriptionColor ?? $this->color;
    }

    public function getDescriptionIcon(): ?string
    {
        return $this->descriptionIcon;
    }

    public function getDescriptionIconPosition(): ?string
    {
        return $this->descriptionIconPosition;
    }

    /**
     * @return array<string, scalar>
     */
    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return $this->shouldOpenUrlInNewTab;
    }

    public function getLabel(): string | Htmlable
    {
        return $this->label;
    }

    public function getId(): string
    {
        return $this->id ?? Str::slug($this->getLabel());
    }

    /**
     * @return scalar | Htmlable | Closure
     */
    public function getValue()
    {
        return value($this->value);
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view('filament-widgets::stats-overview-widget.card', $this->data());
    }
}
