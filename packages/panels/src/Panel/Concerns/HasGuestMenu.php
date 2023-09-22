<?php

namespace Filament\Panel\Concerns;

use Filament\Navigation\MenuItem;

trait HasGuestMenu
{
    /**
     * @var array<MenuItem>
     */
    protected array $guestMenuItems = [];

    protected bool $guestMenuOnSimplePages = false;

    /**
     * @param  array<MenuItem>  $items
     */
    public function guestMenuItems(array $items): static
    {
        $this->guestMenuItems = [
            ...$this->guestMenuItems,
            ...$items,
        ];

        return $this;
    }

    /**
     * @return array<MenuItem>
     */
    public function getGuestMenuItems(): array
    {
        return collect($this->guestMenuItems)
            ->filter(fn (MenuItem $item): bool => $item->isVisible())
            ->sort(fn (MenuItem $item): int => $item->getSort())
            ->all();
    }

    public function guestMenuOnSimplePages(bool $guestMenuOnSimplePages = true): static
    {
        $this->guestMenuOnSimplePages = $guestMenuOnSimplePages;

        return $this;
    }

    public function getGuestMenuOnSimplePages(): bool
    {
        return $this->guestMenuOnSimplePages;
    }
}
