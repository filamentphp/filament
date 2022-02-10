<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanBeOutline
{
    protected bool | Closure $isOutline = false;

    public function outline(bool | Closure $isOutline): static
    {
        $this->isOutline = $isOutline;

        return $this;
    }

    public function isOutline(): bool
    {
        return $this->evaluate($this->isOutline);
    }
}
