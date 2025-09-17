<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class ToggleComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        ksort($color);

        /**
         * Since the toggle doesn't contain text, the icon may be imperative for the user to understand the
         * button's state. Therefore, the color should contrast at least 3:1 with the background to
         * remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-bg-contrast.html
         */
        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 900;

        /**
         * Since the toggle doesn't contain text, the color is imperative for the user to understand the
         * button's state. Therefore, the color should contrast at least 3:1 with the background to
         * remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-bg-contrast.html
         */
        $darkestLightGrayBg = $gray[50];

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($darkestLightGrayBg, $color[$shade])) {
                $bg = $shade;

                break;
            }
        }

        $bg ??= 900;

        krsort($color);

        $lightestDarkGrayBg = $gray[700];

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
                $darkBg = $shade;

                break;
            }
        }

        $darkBg ??= 200;

        return [
            'bg' => $bg,
            'text' => $text,
            'dark:bg' => $darkBg,
        ];
    }
}
