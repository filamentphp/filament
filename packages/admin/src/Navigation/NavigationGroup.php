<?php

namespace Filament\Navigation;

use Illuminate\Contracts\Support\Arrayable;

class NavigationGroup
{
    protected bool $isCollapsed = false;

    protected ?bool $isCollapsible = null;

    protected ?string $icon = null;

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
        return $this->isCollapsible ?? config('filament.layout.sidebar.groups.are_collapsible') ?? true;
    }
}
