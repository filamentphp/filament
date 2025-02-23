<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasColor
{
    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string | array | Closure | null $color = null;

    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string | array | Closure | null $defaultColor = null;

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function color(string | array | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function defaultColor(string | array | Closure | null $color): static
    {
        $this->defaultColor = $color;

        return $this;
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getColor(): string | array | null
    {
        return $this->evaluate($this->color) ?? $this->evaluate($this->defaultColor);
    }
}
