<?php

namespace Filament\Infolists\Components\Concerns;

use BackedEnum;
use Closure;
use Filament\Infolists\Components\Component;
use Filament\Support\Contracts\HasColor as ColorInterface;

trait HasColor
{
    protected string | Closure | null $color = null;

    public function color(string | Closure | null $color): static
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

    public function getColor(): ?string
    {
        $color = $this->evaluate($this->color);

        $enum = $color ?? $this->enum;
        if (
            is_string($enum) &&
            function_exists('enum_exists') &&
            enum_exists($enum) &&
            is_a($enum, BackedEnum::class, allow_string: true) &&
            is_a($enum, ColorInterface::class, allow_string: true)
        ) {
            return $enum::tryFrom($this->getState())?->getColor();
        }

        return $color;
    }
}
