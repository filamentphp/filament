<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasColors
{
    /**
     * @var array<array<string, array<int | string, string | int> | string> | Closure>
     */
    protected array $colors = [];

    /**
     * @param  array<string, array<int | string, string | int> | string> | Closure  $colors
     */
    public function colors(array | Closure $colors): static
    {
        $this->colors[] = $colors;

        return $this;
    }

    /**
     * @return array<string, array<int | string, string | int> | string>
     */
    public function getColors(): array
    {
        $colors = [];

        foreach ($this->colors as $set) {
            $set = $this->evaluate($set);

            foreach ($set as $name => $color) {
                $colors[$name] = $color;
            }
        }

        return $colors;
    }
}
