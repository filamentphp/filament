<?php

namespace Filament\Panel\Concerns;

use BackedEnum;

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
        $this->icons = [
            ...$this->icons,
            ...$icons,
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
