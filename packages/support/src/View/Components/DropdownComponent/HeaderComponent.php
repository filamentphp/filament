<?php

namespace Filament\Support\View\Components\DropdownComponent;

use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class HeaderComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        return ComponentColorMap::make($color)
            ->slot('text', surface: 'oklch(1 0 0)', minShade: 600, fallback: 950)
            ->slot('dark:text', surface: $gray[900], maxShade: 400, shouldStartFromDarkest: true, fallback: 200)
            ->get();
    }
}
