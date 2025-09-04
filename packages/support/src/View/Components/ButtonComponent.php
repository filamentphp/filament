<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class ButtonComponent implements HasColor, HasDefaultGrayColor
{
    public function __construct(
        public readonly bool $isOutlined = false,
    ) {}

    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        if ($this->isOutlined) {
            return $this->getOutlinedColorClasses($color);
        }

        $textColors = [];

        ksort($color);

        $possibleDarkTextColors = $color; // Copy the array so we can remove elements from it
        unset($possibleDarkTextColors[array_key_first($color)]); // It is not possible for the text color to be the same as the first background color

        $is50AccessibleOnDarkerShades = true;

        foreach (array_keys($color) as $shade) {
            foreach ($possibleDarkTextColors as $possibleDarkTextColorShade => $possibleDarkTextColor) {
                if (($possibleDarkTextColorShade >= 800) && Color::isTextContrastRatioAccessible($color[$shade], $possibleDarkTextColor)) {
                    $textColors[$shade] = $possibleDarkTextColorShade;

                    continue 2;
                }

                unset($possibleDarkTextColors[$possibleDarkTextColorShade]); // If it is not possible for this text color to be accessible, it's not possible for a darker color to find a match either, until all dark colors have been tried.
            }

            if (
                $is50AccessibleOnDarkerShades &&
                array_key_exists(50, $color)
            ) {
                if (Color::isTextContrastRatioAccessible($color[$shade], $color[50])) {
                    $textColors[$shade] = 50;

                    continue;
                } else {
                    $is50AccessibleOnDarkerShades = false;
                }
            }

            $textColors[$shade] = 0;
        }

        $textLightnessIndex = [
            300 => ($textColors[300] === 0) || Color::isLight($color[$textColors[300]]),
            400 => ($textColors[400] === 0) || Color::isLight($color[$textColors[400]]),
            500 => ($textColors[500] === 0) || Color::isLight($color[$textColors[500]]),
            600 => ($textColors[600] === 0) || Color::isLight($color[$textColors[600]]),
        ];

        if ($textLightnessIndex[600] && $textLightnessIndex[500]) {
            $bg = 600;
            $hoverBg = 500;
        } else {
            $bg = 400;
            $hoverBg = ($textLightnessIndex[400] === $textLightnessIndex[300]) ? 300 : 500;
        }

        $darkBg = 600;
        $darkHoverBg = ($textLightnessIndex[600] === $textLightnessIndex[500])
            ? 500
            : 700;

        $text = $textColors[$bg];
        $hoverText = $textColors[$hoverBg];
        $darkText = $textColors[$darkBg];
        $darkHoverText = $textColors[$darkHoverBg];

        return [
            'bg' => $bg,
            'hover:bg' => $hoverBg,
            'dark:bg' => $darkBg,
            'dark:hover:bg' => $darkHoverBg,
            'text' => $text,
            'hover:text' => $hoverText,
            'dark:text' => $darkText,
            'dark:hover:text' => $darkHoverText,
        ];
    }

    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getOutlinedColorClasses(array $color): array
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
            if ($shade > 500) {
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
