<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasReorderAnimationDuration
{
    protected int | Closure $reorderAnimationDuration = 300;

    public function reorderAnimationDuration(int | Closure $duration): static
    {
        $this->reorderAnimationDuration = $duration;

        return $this;
    }

    public function getReorderAnimationDuration(): int
    {
        return $this->evaluate($this->reorderAnimationDuration);
    }
}
