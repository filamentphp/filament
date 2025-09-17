<?php

namespace Filament\Support\View\Components\DropdownComponent;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class ItemComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        ksort($color);

        foreach (array_keys($color) as $shade) {
            if ($shade < 600) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 950;

        foreach (array_keys($color) as $shade) {
            if ($shade < 600) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible($color[50], $color[$shade])) {
                $hoverText = $shade;

                break;
            }
        }

        $hoverText ??= 950;

        krsort($color);

        foreach (array_keys($color) as $shade) {
            if ($shade > 400) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible($gray[900], $color[$shade])) {
                $darkText = $shade;

                break;
            }
        }

        $darkText ??= 200;

        foreach (array_keys($color) as $shade) {
            if ($shade > 400) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible($color[950], $color[$shade])) {
                $darkHoverText = $shade;

                break;
            }
        }

        $darkHoverText ??= 100;

        return [
            'text' => $text,
            'hover:text' => $hoverText,
            'dark:text' => $darkText,
            'dark:hover:text' => $darkHoverText,
        ];
    }
}
