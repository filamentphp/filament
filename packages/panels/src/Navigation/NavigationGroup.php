<?php

namespace Filament\Navigation;

use Closure;
use Exception;
use Filament\Support\Components\Component;
use Illuminate\Contracts\Support\Arrayable;

class NavigationGroup extends Component
{
    protected bool | Closure $isCollapsed = false;

    protected bool | Closure | null $isCollapsible = null;

    protected string | Closure | null $icon = null;

    /**
     * @var array<NavigationItem> | Arrayable
     */
    protected array | Arrayable $items = [];

    protected string | Closure | null $label = null;

    final public function __construct(string | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    public function collapsed(bool | Closure $condition = true): static
    {
        $this->isCollapsed = $condition;

        $this->collapsible($condition);

        return $this;
    }

    public function collapsible(bool | Closure | null $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<NavigationItem> | Arrayable  $items
     */
    public function items(array | Arrayable $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getIcon(): ?string
    {
        $icon = $this->evaluate($this->icon);

        if ($this->hasItemIcons() && filled($icon)) {
            throw new Exception("Navigation group [{$this->getLabel()}] has an icon but one or more of its items also have icons. Either the group or its items can have icons, but not both. This is to ensure a proper user experience.");
        }

        return $icon;
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
        return $this->evaluate($this->label);
    }

    public function isCollapsed(): bool
    {
        return (bool) $this->evaluate($this->isCollapsed);
    }

    public function isCollapsible(): bool
    {
        return (bool) ($this->evaluate($this->isCollapsible) ?? filament()->hasCollapsibleNavigationGroups());
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

        if (($hasIconCount > 0) && ($hasNoIconCount > 0) && filled($label = $this->getLabel())) {
            throw new Exception("Navigation group [{$label}] has items with and without icons. All items must have icons or none of them can have icons. This is to ensure a proper user experience.");
        }

        return $hasIconCount > 0;
    }
}
