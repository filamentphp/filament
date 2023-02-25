<?php

namespace Filament\Navigation;

use Illuminate\Contracts\Support\Arrayable;

class NavigationGroup
{
    protected bool $isCollapsed = false;

    protected ?bool $isCollapsible = null;

    protected ?string $icon = null;

    /**
     * @var array<NavigationItem | NavigationGroup> | Arrayable
     */
    protected array | Arrayable $items = [];

    protected ?string $label = null;

    final public function __construct(?string $label = null)
    {
        $this->label($label);
    }

    public static function make(?string $label = null): static
    {
        return app(static::class, ['label' => $label]);
    }

    public function collapsed(bool $condition = true): static
    {
        $this->isCollapsed = $condition;

        $this->collapsible();

        return $this;
    }

    public function collapsible(?bool $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<NavigationItem | NavigationGroup> | Arrayable  $items
     */
    public function items(array | Arrayable $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return array<NavigationItem | NavigationGroup> | Arrayable
     */
    public function getItems(): array | Arrayable
    {
        return $this->items;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function isCollapsed(): bool
    {
        return $this->isCollapsed;
    }

    public function isCollapsible(): bool
    {
        return $this->isCollapsible ?? filament()->hasCollapsibleNavigationGroups();
    }

    public function isActive(): bool
    {
        foreach ($this->getItems() as $item) {
            if (! $item->isActive()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
