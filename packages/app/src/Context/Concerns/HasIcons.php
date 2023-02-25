<?php

namespace Filament\Context\Concerns;

use Filament\Support\Icons\Icon;

trait HasIcons
{
    /**
     * @var array<string, Icon>
     */
    protected array $icons = [];

    /**
     * @param  array<string, Icon>  $icons
     */
    public function icons(array $icons): static
    {
        $this->icons = array_merge($this->icons, $icons);

        return $this;
    }
}
