<?php

namespace Filament\Panel\Concerns;

use Filament\Navigation\MenuItem;

trait HasUserMenu
{
    /**
     * @var array<MenuItem>
     */
    protected array $userMenuItems = [];

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
