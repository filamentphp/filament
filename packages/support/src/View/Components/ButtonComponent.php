<?php

namespace Filament\Support\View\Components;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class ButtonComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @var array<int, static>
     */
    protected static array $cachedInstances = [];

    public function __construct(
        public readonly bool $isOutlined = false,
    ) {}

    public static function make(bool $isOutlined = false): static
    {
        return static::$cachedInstances[$isOutlined] ??= app(static::class, ['isOutlined' => $isOutlined]);
    }

    /**
     * @param  array<int, string>  $color
     * @return array<string, int>
     */
    public function getColorMap(array $color): array
    {
        $gray = FilamentColor::getColor('gray');

        if ($this->isOutlined) {
            return ComponentColorMap::make($color)
                ->slot('text', surface: $gray[50], minRatio: Color::WCAG_AA_TEXT, fallback: 900)
                ->slot('dark:text', surface: $gray[700], minRatio: Color::WCAG_AA_TEXT, maxShade: 500, shouldStartFromDarkest: true, fallback: 200)
                ->get();
        }

        return ButtonComponentColorMap::make($color)
            ->minContrastRatio(Color::WCAG_AA_TEXT)
            ->lightBackground(bg: 600, hover: 500)
            ->lightBackground(bg: 400, hover: 300, alternateHover: 500)
            ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
            ->get();
    }
}
