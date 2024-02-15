<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasPivotAttributes
{
    protected array | Closure | null $pivotAttributes = null;

    public function pivotAttributes(array | Closure | null $attributes): static
    {
        $this->pivotAttributes = $attributes;

        return $this;
    }

    public function getPivotAttributes(): ?array
    {
        return $this->evaluate($this->pivotAttributes) ?? [];
    }
}
