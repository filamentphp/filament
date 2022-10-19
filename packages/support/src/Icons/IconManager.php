<?php

namespace Filament\Support\Icons;

use BladeUI\Icons\Svg;

class IconManager
{
    protected array $defaultIcons = [];

    protected array $icons = [];

    public function register(array $icons = []): static
    {
        $this->icons = array_merge($this->icons, $icons);

        return $this;
    }

    public function registerDefaults(array $icons = []): static
    {
        $this->defaultIcons = array_merge($this->defaultIcons, $icons);

        return $this;
    }

    public function resolve(string $name): string
    {
        return $this->icons[$name] ?? $this->defaultIcons[$name] ?? $name;
    }

    public function render(string $icon, string | array | null $class = null, array $attributes = []): Svg
    {
        return svg(
            $this->resolve($icon),
            $class,
            $attributes,
        );
    }
}
