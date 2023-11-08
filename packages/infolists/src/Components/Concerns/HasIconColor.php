<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\Component;

trait HasIconColor
{
    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null
     */
    protected string | array | bool | Closure | null $iconColor = null;

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null  $color
     */
    public function iconColor(string | array | bool | Closure | null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $colors
     */
    public function iconColors(array | Closure $colors): static
    {
        $this->iconColor(function (Component $component, $state) use ($colors) {
            $colors = $component->evaluate($colors);

            $color = null;

            foreach ($colors as $conditionalColor => $condition) {
                if (is_numeric($conditionalColor)) {
                    $color = $condition;
                } elseif ($condition instanceof Closure && $component->evaluate($condition)) {
                    $color = $conditionalColor;
                } elseif ($condition === $state) {
                    $color = $conditionalColor;
                }
            }

            return $color;
        });

        return $this;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getIconColor(mixed $state): string | array | null
    {
        $color = $this->evaluate($this->iconColor, [
            'state' => $state,
        ]);

        if ($color === false) {
            return null;
        }

        if (filled($color)) {
            return $color;
        }

        return null;
    }
}
