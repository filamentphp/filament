<?php

namespace Filament\Support\Icons;

class IconManager
{
    protected array $icons = [];

    public function register(array $icons): void
    {
        $this->icons = array_merge($this->icons, $icons);
    }

    public function resolve(string $name): ?Icon
    {
        return $this->icons[$name] ?? null;
    }
}
