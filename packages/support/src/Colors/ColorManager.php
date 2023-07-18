<?php

namespace Filament\Support\Colors;

use Spatie\Color\Hex;

class ColorManager
{
    /**
     * @var array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}>
     */
    protected array $colors = [];

    /**
     * @param  array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string>  $colors
     */
    public function register(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->colors[$name] = $this->processColor($color);
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

        if (is_array($color)) {
            return array_map(function (string $color): string {
                if (str_starts_with($color, '#')) {
                    $color = Hex::fromString($color)->toRgb();

                    return "{$color->red()}, {$color->green()}, {$color->blue()}";
                }

                if (str_starts_with($color, 'rgb')) {
                    return (string) str($color)
                        ->after('rgb(')
                        ->before(')');
                }

                return $color;
            }, $color);
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
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Amber,
            'success' => Color::Green,
            'warning' => Color::Amber,
            ...$this->colors,
        ];
    }
}
