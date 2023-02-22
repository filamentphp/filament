<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\Component;
use Filament\Support\Contracts\HasColor as ColorInterface;

trait HasColor
{
    protected string | bool | Closure | null $color = null;

    public function color(string | bool | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $colors
     */
    public function colors(array | Closure $colors): static
    {
        $this->color(function (Component $component, $state) use ($colors) {
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

    public function getColor(mixed $state): ?string
    {
        $color = $this->evaluate($this->color, [
            'state' => $state,
        ]);

        if ($color === false) {
            return null;
        }

        if (filled($color)) {
            return $color;
        }

        if (! $state instanceof ColorInterface) {
            return null;
        }

        return $state->getColor();
    }
}
