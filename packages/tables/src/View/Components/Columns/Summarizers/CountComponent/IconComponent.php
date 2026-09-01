<?php

namespace Filament\Tables\View\Components\Columns\Summarizers\CountComponent;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * Since the icons in the count summarizer are imperative for the user to understand the
     * summary, the icon should have a color that contrasts at least 3:1 with the surface to
     * remain compliant with WCAG AA standards.
     *
     * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-text-contrast.html
     *
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        return ComponentColorMap::make($color)
            ->slot('text', surface: 'oklch(1 0 0)', minRatio: Color::WCAG_AA_NON_TEXT, fallback: 900)
            ->slot('dark:text', surface: $gray[900], minRatio: Color::WCAG_AA_NON_TEXT, shouldStartFromDarkest: true, fallback: 200)
            ->get();
    }
}
