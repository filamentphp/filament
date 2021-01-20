<?php

namespace Filament;

class Navigation
{
    protected array $items = [];

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
    }

    public function __set(string $name, array $item)
    {
        $this->items[$name] = $item;
    }

    public function items()
    {
        return collect($this->items);
    }
}
