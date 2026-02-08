<?php

namespace Filament\Tables\View\Components\Columns\TextColumnComponent;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
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

        ksort($color);

        foreach (array_keys($color) as $shade) {
            if (Color::isTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
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

            if (Color::isTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
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
