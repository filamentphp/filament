<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Column;

trait HasColor
{
    protected string | Closure | null $color = null;

    public function color(string | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function colors(array | Closure $colors): static
    {
        $this->color(function (Column $column, $state) use ($colors) {
            $colors = $column->evaluate($colors, ['state' => $state]);

            $color = null;

            foreach ($colors as $conditionalColor => $condition) {
                if (is_numeric($conditionalColor)) {
                    $color = $condition;
                } elseif ($condition instanceof Closure && $column->evaluate($condition, ['state' => $state])) {
                    $color = $conditionalColor;
                } elseif ($condition === $state) {
                    $color = $conditionalColor;
                }
            }

            return $color;
        });

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->color, ['state' => $this->getState()]);
    }
}
