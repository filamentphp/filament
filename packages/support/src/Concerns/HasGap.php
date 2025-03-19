<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasGap
{
    protected int | string | Closure $gap = 6;

    public function gap(int | string | Closure $gap): static
    {
        $this->gap = $gap;

        return $this;
    }

    public function getGap(): int | string
    {
        return $this->evaluate($this->gap);
    }
}
