<?php

namespace Filament\Panel\Concerns;

trait HasIcons
{
    /**
     * @var array<string, string>
     */
    protected array $icons = [];

    /**
     * @param  array<string, string>  $icons
     */
    public function icons(array $icons): static
    {
        $this->icons = [
            ...$this->icons,
            ...$icons,
        ];

        return $this;
    }
}
