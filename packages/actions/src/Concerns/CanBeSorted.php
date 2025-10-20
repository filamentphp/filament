<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeSorted
{
    protected int | Closure | null $sort = null;

    public function sort(int | Closure | null $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort(): int
    {
        return $this->evaluate($this->sort) ?? 0;
    }
}
