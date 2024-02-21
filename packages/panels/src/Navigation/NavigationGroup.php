<?php

namespace Filament\Navigation;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasExtraSidebarAttributes;
use Filament\Support\Concerns\HasExtraTopbarAttributes;
use Illuminate\Contracts\Support\Arrayable;

class NavigationGroup extends Component
{
    use HasExtraSidebarAttributes;
    use HasExtraTopbarAttributes;

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
        return $this->evaluate($this->icon);
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
}
