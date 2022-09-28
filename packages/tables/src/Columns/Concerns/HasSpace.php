<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasSpace
{
    protected int | Closure | null $space = null;

    public function space(int | Closure | null $space = 1): static
    {
        $this->space = $space;

        return $this;
    }

    public function getSpace(): ?int
    {
        return $this->evaluate($this->space);
    }
}
