<?php

namespace Filament\Infolists\View\Components\TextEntryComponent;

use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;

class ItemComponent implements HasColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        return ComponentColorMap::make($color)
            ->slot('text', surface: 'oklch(1 0 0)', fallback: 900)
            ->slot('dark:text', surface: $gray[800], maxShade: 600, shouldStartFromDarkest: true, fallback: 200)
            ->get();
    }
}
