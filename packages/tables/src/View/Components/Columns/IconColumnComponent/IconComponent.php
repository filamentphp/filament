<?php

namespace Filament\Tables\View\Components\Columns\IconColumnComponent;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        /**
         * Since the icons in the column are the only content, they should have a color that contrasts
         * at least 3:1 with the background to remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-text-contrast.html
         */
        ksort($color);

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 900;

        krsort($color);

        $lightestDarkGrayBg = $gray[800];

        foreach (array_keys($color) as $shade) {
            if ($shade > 600) {
                continue;
            }

            if (Color::isNonTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
                $darkText = $shade;

                break;
            }
        }

        $darkText ??= 200;

        return [
            'text' => $text,
            'dark:text' => $darkText,
        ];
    }
}
