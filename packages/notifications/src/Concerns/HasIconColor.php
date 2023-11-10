<?php

namespace Filament\Notifications\Concerns;

use Filament\Support\Concerns\HasIconColor as BaseTrait;

trait HasIconColor
{
    use BaseTrait {
        getIconColor as baseGetIconColor;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getIconColor(): string | array | null
    {
        return $this->baseGetIconColor() ?? $this->getStatus();
    }
}
