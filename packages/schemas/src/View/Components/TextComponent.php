<?php

namespace Filament\Schemas\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
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

        ksort($color);

        $darkestLightGrayBg = $gray[50];

        foreach (array_keys($color) as $shade) {
            if (Color::isTextContrastRatioAccessible($darkestLightGrayBg, $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 900;

        krsort($color);

        $lightestDarkGrayBg = $gray[700];

        foreach (array_keys($color) as $shade) {
            if ($shade > 400) {
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
