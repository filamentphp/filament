<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasReorderAnimationDuration
{
    protected int | Closure $reorderAnimationDuration = 300;

    public function reorderAnimationDuration(int | Closure $animation): static
    {
        $this->reorderAnimationDuration = $animation;

        return $this;
    }

    public function getReorderAnimationDuration(): int
    {
        return $this->evaluate($this->reorderAnimationDuration);
    }
}
