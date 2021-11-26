<?php

namespace Filament\Navigation;

use Closure;

class NavigationItem
{
    protected ?string $group = null;

    protected ?Closure $isActiveWhen = null;

    protected string $icon;

    protected string $label;

    protected ?int $sort = null;

    protected ?string $url = null;

    public static function make(): static
    {
        return new static();
    }

    public function group(?string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function isActiveWhen(Closure $callback): static
    {
        $this->isActiveWhen = $callback;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sort(?int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function url(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSort(): int
    {
        return $this->sort ?? -1;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function isActive(): bool
    {
        $callback = $this->isActiveWhen;

        if ($callback === null) {
            return false;
        }

        return app()->call($callback);
    }
}
