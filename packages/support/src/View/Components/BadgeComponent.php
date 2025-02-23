<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class BadgeComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        ksort($color);

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($color[50], $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 900;

        krsort($color);

        $gray = FilamentColor::getColor('gray');
        $lightestDarkGrayBg = $gray[500];

        foreach (array_keys($color) as $shade) {
            if ($shade > 500) {
                continue;
            }

            if (Color::isNonTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
                $darkText = $shade;

                break;
            }
        }

        $darkText ??= 200;

        return [
            "fi-text-color-{$text}",
            "dark:fi-text-color-{$darkText}",
        ];
    }
}
