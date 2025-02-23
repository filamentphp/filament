<?php

namespace Filament\Notifications\Concerns;

use Filament\Support\Concerns\HasIconColor as BaseTrait;

trait HasIconColor
{
    use BaseTrait {
        getIconColor as getBaseIconColor;
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getIconColor(): string | array | null
    {
        return $this->getBaseIconColor() ?? $this->getStatus();
    }
}
