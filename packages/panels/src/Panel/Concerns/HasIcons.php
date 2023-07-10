<?php

namespace Filament\Panel\Concerns;

use Filament\Support\Icons\Icon;

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
