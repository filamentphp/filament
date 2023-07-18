<?php

namespace Filament\Support\Icons;

class IconManager
{
    /**
     * @var array<string, string>
     */
    protected array $icons = [];

    /**
     * @param  array<string, string>  $icons
     */
    public function register(array $icons): void
    {
        $this->icons = [
            ...$this->icons,
            ...$icons,
        ];
    }

    public function resolve(string $name): ?string
    {
        return $this->icons[$name] ?? null;
    }
}
