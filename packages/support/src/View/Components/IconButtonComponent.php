<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\IconButtonComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconButtonComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * Since the icon button doesn't contain text, the icon is imperative for the user to
     * understand the button's purpose. Therefore, the icon should have a color that contrasts
     * at least 3:1 with the surface to remain compliant with WCAG AA standards.
     *
     * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-text-contrast.html
     *
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        return IconButtonComponentColorMap::make($color)
            ->minContrastRatio(Color::WCAG_AA_NON_TEXT)
            ->lightSurface($gray[50])
            ->darkSurface($gray[700])
            ->darkMaxShade(500)
            ->get();
    }
}
