<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasColors
{
    /**
     * @var array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string> | Closure
     */
    protected array | Closure $colors = [];

    /**
     * @param  array<string, array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | string> | Closure  $colors
     */
    public function colors(array | Closure $colors): static
    {
        if ($colors instanceof Closure) {
            $this->colors = $colors;

            return $this;
        }
        foreach ($colors as $name => $color) {
            $this->colors[$name] = $color;
        }

        return $this;
    }
}
