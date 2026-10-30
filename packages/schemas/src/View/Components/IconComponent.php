<?php

namespace Filament\Schemas\View\Components;

use Filament\Support\View\Components\Contracts\HasColorMap;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconComponent implements HasColorMap, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        return [];
    }
}
