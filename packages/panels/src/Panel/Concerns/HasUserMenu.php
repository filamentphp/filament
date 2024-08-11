<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Navigation\MenuItem;

trait HasUserMenu
{
    /**
     * @var array<MenuItem>
     */
    protected array $userMenuItems = [];

    protected bool | Closure $isVisible = true;

    protected bool | Closure $onlyUserMenuItems = false;

    public function userMenu(bool | Closure $isVisible = true, bool | Closure $onlyUserMenuItems = false): static
    {
        $this->isVisible = $isVisible;
        $this->onlyUserMenuItems = $onlyUserMenuItems;

        return $this;
    }

    public function hasUserMenu(): bool
    {
        return (bool) $this->evaluate($this->isVisible);
    }

    public function getUserMenu(): array
    {
        return [
            'isVisible' => $this->isVisible,
            'onlyUserMenuItems' => $this->onlyUserMenuItems,
        ];
    }

    /**
     * @param  array<MenuItem>  $items
     */
    public function userMenuItems(array $items): static
    {
        $this->userMenuItems = [
            ...$this->userMenuItems,
            ...$items,
        ];

        return $this;
    }

    /**
     * @return array<MenuItem>
     */
    public function getUserMenuItems(): array
    {
        return collect($this->userMenuItems)
            ->filter(fn (MenuItem $item): bool => $item->isVisible())
            ->sort(fn (MenuItem $item): int => $item->getSort())
            ->all();
    }
}
