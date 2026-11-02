<?php

namespace Filament\Panel\Concerns;

use BackedEnum;
use Filament\Support\Icons\IconManager;

trait HasIcons
{
    /**
     * @var array<string, string | BackedEnum>
     */
    protected array $icons = [];

    /**
     * @param  array<string, string | BackedEnum>  $icons
     */
    public function icons(array $icons): static
    {
        $registeredIcons = [];

        foreach ($icons as $alias => $icon) {
            $registeredIcons[IconManager::normalizeAlias($alias)] = $icon;
        }

        $this->icons = [
            ...$this->icons,
            ...$registeredIcons,
        ];

        return $this;
    }

    /**
     * @return array<string, string | BackedEnum>
     */
    public function getIcons(): array
    {
        return $this->icons;
    }
}
