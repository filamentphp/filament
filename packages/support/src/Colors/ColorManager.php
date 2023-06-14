<?php

namespace Filament\Support\Colors;

use Filament\Support\Colors\Color;
use Filament\Support\Colors\Concerns\CanConfigureColors;

class ColorManager
{
    /**
     * @var array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}>
     */
    protected array $colors = [];

    /**
     * @param array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string> $colors
     */
    public function register(array $colors): static
    {
        foreach ($colors as $colorName => $color) {
            $this->colors[$colorName] = $this->processColor($color);
        }

        return $this;
    }

    /**
     * @param  array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string  $color
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string
     */
    public function processColor(array | string $color): array | string
    {
        if (is_string($color) && str_starts_with($color, '#')) {
            return Color::hex($color);
        }

        if (is_string($color) && str_starts_with($color, 'rgb')) {
            return Color::rgb($color);
        }

        return $color;
    }

    /**
     * @return array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}>
     */
    public function getColors(): array
    {
        return [
            'danger' => Color::Red,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'primary' => Color::Amber,
            'secondary' => Color::Gray,
            'success' => Color::Green,
            'warning' => Color::Amber,
            ...$this->colors,
        ];
    }
}
