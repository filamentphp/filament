<?php

namespace Filament\Helpers;

use Illuminate\Support\Collection;

class Navigation {
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function __set(string $name, array $item): void
    {
        $this->items[$name] = $item;
    }

    /**
     * @param string $name
     * @return null|array
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
    }

    public function items(): Collection
    {
        return collect($this->items);
    }
}