<?php

namespace Filament\Widgets\StatsOverviewWidget;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Card extends Component implements Htmlable
{
    protected ?array $chart = null;

    protected ?string $chartColor = null;

    protected ?string $color = null;

    protected ?string $icon = null;

    protected string | Htmlable | null $description = null;

    protected ?string $descriptionIcon = null;

    protected ?string $descriptionColor = null;

    protected array $extraAttributes = [];

    protected bool $shouldOpenUrlInNewTab = false;

    protected ?string $url = null;

    protected ?string $id = null;

    protected string | Htmlable $label;

    protected $value;

    final public function __construct(string $label, $value)
    {
        $this->label($label);
        $this->value($value);
    }

    public static function make(string $label, $value): static
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

    public function descriptionIcon(?string $icon): static
    {
        $this->descriptionIcon = $icon;

        return $this;
    }

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

    public function value($value): static
    {
        $this->value = $value;

        return $this;
    }

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

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
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
        return view('filament::widgets.stats-overview-widget.card', $this->data());
    }
}
