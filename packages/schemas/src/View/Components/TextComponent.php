<?php

namespace Filament\Schemas\View\Components;

use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class TextComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        return ComponentColorMap::make($color)
            ->slot('text', surface: $gray[50], fallback: 900)
            ->slot('dark:text', surface: $gray[700], maxShade: 400, shouldStartFromDarkest: true, fallback: 200)
            ->get();
    }
}
