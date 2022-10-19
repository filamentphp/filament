<?php

namespace Filament\Support\Icons;

use BladeUI\Icons\Svg;
use Exception;
use Illuminate\Support\Arr;

class IconManager
{
    protected array $icons = [];

    public function register(array $icons = []): static
    {
        $this->icons = array_merge($this->icons, $icons);

        return $this;
    }

    public function resolve(string $name): ?Icon
    {
        return $this->icons[$name] ?? null;
    }
}
