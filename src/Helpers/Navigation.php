<?php

namespace Filament\Helpers;

class Navigation 
{
    private $items = [];
    
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function __get($name)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->items[$name] = $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}
