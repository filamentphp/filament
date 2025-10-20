<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Support\Contracts\HasColor as ColorInterface;

trait HasColor
{
    /**
     * @var string | array<string> | bool | Closure | null
     */
    protected string | array | bool | Closure | null $color = null;

    /**
     * @param  string | array<string> | bool | Closure | null  $color
     */
    public function color(string | array | bool | Closure | null $color): static
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

    /**
     * @return string | array<string> | null
     */
    public function getColor(mixed $state): string | array | null
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
