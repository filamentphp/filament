<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\Contracts\HasColor as ColorInterface;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

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
        $this->color(function (Column $column, $state, ?Model $relatedRecord) use ($colors) {
            $colors = $column->evaluate($colors, [
                'state' => $state,
                'relatedRecord' => $relatedRecord,
            ]);

            $color = null;

            foreach ($colors as $conditionalColor => $condition) {
                if (is_numeric($conditionalColor)) {
                    $color = $condition;
                } elseif ($condition instanceof Closure && $column->evaluate($condition, [
                    'state' => $state,
                    'relatedRecord' => $relatedRecord,
                ])) {
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
    public function getColor(mixed $state, ?Model $relatedRecord = null): string | array | null
    {
        $color = $this->evaluate($this->color, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
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
