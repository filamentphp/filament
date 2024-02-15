<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasPivotAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array | Closure $pivotAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function pivotAttributes(array | Closure $attributes): static
    {
        $this->pivotAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getPivotAttributes(): ?array
    {
        return $this->evaluate($this->pivotAttributes);
    }
}
