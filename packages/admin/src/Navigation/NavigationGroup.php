<?php

namespace Filament\Navigation;

use Illuminate\Support\Arr;

class NavigationGroup
{
    protected ?string $icon = null;

    protected ?string $label = null;

    protected ?int $sort = null;

    /** @var \Filament\Navigation\NavigationItem[] */
    protected array $items = [];

    protected bool $collapsible = true;

    protected bool $collapsed = false;

    final public function __construct()
    {
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /** @param \Filament\Navigation\NavigationItem[] $items */
    public function items(array $items): static
    {
        $this->items = collect($items)->map(
            fn (NavigationItem $item, int $index) => $item->group($this->label)->sort($index),
        )->toArray();

        return $this;
    }

    public function item(NavigationItem $item): static
    {
        $this->items[] = $item->group($this->label);

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function sort(?int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSort(): int
    {
        return $this->sort ?? -1;
    }

    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    public function isCollapsed(): bool
    {
        return $this->collapsed;
    }
}
