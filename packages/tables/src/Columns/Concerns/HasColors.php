<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

trait HasColors
{
    protected string | Closure | null $color = null;

    public function color(string | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function colors(array | Closure $colors): static
    {
        $this->color(function (Column $column, Model $record, $state) use ($colors) {
            $colors = $column->evaluate($colors);
            $stateColor = null;

            foreach ($colors as $color => $condition) {
                if (is_numeric($color)) {
                    $stateColor = $condition;
                } elseif ($condition instanceof Closure && $condition($state, $record)) {
                    $stateColor = $color;
                } elseif ($condition === $state) {
                    $stateColor = $color;
                }
            }

            return $stateColor;
        });

        return $this;
    }

    public function getStateColor(): ?string
    {
        return $this->evaluate($this->color, [
            'state' => $this->getState(),
        ]);
    }
}
