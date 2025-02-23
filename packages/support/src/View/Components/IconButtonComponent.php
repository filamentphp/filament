<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconButtonComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        /**
         * Since the icon button doesn't contain text, the icon is imperative for the user to understand the
         * button's purpose. Therefore, the icon should have a color that contrasts at least 3:1 with the
         * background to remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-text-contrast.html
         */
        ksort($color);

        $darkestLightGrayBg = $gray[50];

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($darkestLightGrayBg, $color[$shade])) {
                if ($shade > 500) {
                    // Shades above 500 are likely to be quite dark, so instead of lightening the button
                    // when it is hovered, we darken it.
                    $text = $shade;
                    $hoverText = $shade + 100;
                } else {
                    $text = $shade + 100;
                    $hoverText = $shade;
                }

                break;
            }
        }

        $text ??= 900;
        $hoverText ??= 800;

        krsort($color);

        $lightestDarkGrayBg = $gray[700];

        foreach (array_keys($color) as $shade) {
            if ($shade > 500) {
                continue;
            }

            if (Color::isNonTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
                $darkText = $shade;
                $darkHoverText = $shade - 100;

                break;
            }
        }

        $darkText ??= 200;
        $darkHoverText ??= 100;

        return [
            "fi-text-color-{$text}",
            "hover:fi-text-color-{$hoverText}",
            "dark:fi-text-color-{$darkText}",
            "dark:hover:fi-text-color-{$darkHoverText}",
        ];
    }
}
