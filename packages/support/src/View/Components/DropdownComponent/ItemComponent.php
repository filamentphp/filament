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
     * @return array<string>
     */
    public function getColorClasses(array $color): array
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
            "fi-text-color-{$text}",
            "hover:fi-text-color-{$hoverText}",
            "dark:fi-text-color-{$darkText}",
            "dark:hover:fi-text-color-{$darkHoverText}",
        ];
    }
}
