<?php

namespace Filament\Navigation;

use Exception;
use Illuminate\Contracts\Support\Arrayable;

class NavigationGroup
{
    protected bool $isCollapsed = false;

    protected ?bool $isCollapsible = null;

    protected ?string $icon = null;

    /**
     * @var array<NavigationItem> | Arrayable
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
     * @param  array<NavigationItem> | Arrayable  $items
     */
    public function items(array | Arrayable $items): static
    {
        foreach ($items as $item) {
            if ($item instanceof NavigationItem) {
                continue;
            }

            throw new Exception("Navigation group [{$this->getLabel()}] has a nested group, which is not supported.");
        }

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
        if (filled($this->icon) && $this->hasItemIcons()) {
            throw new Exception("Navigation group [{$this->getLabel()}] has an icon but one or more of its items also have icons. Either the group or its items can have icons, but not both. This is to ensure a proper user experience.");
        }

        return $this->icon;
    }

    /**
     * @return array<NavigationItem> | Arrayable
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

    public function hasItemIcons(): bool
    {
        $hasIconCount = 0;
        $hasNoIconCount = 0;

        foreach ($this->getItems() as $item) {
            if (! $item instanceof NavigationItem) {
                continue;
            }

            if (blank($item->getIcon())) {
                $hasNoIconCount++;

                continue;
            }

            $hasIconCount++;
        }

        if (($hasIconCount > 0) && ($hasNoIconCount > 0)) {
            throw new Exception("Navigation group [{$this->getLabel()}] has items with and without icons. All items must have icons or none of them can have icons. This is to ensure a proper user experience.");
        }

        return $hasIconCount > 0;
    }
}
