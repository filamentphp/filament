<?php

namespace Filament\Panel\Concerns;

trait HasColors
{
    /**
     * @var array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string>
     */
    protected array $colors = [];

    /**
     * @param  array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string>  $colors
     */
    public function colors(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->colors[$name] = $color;
        }

        return $this;
    }
}
