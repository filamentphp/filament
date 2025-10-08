<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanQuietlyProcess
{
    protected bool | Closure | null $quietly = null;

    public function quietly(bool | Closure $quietly = true): static
    {
        $this->quietly = $quietly;

        return $this;
    }

    protected function shouldQuietlyProcess(): bool
    {
        if (! $this->quietly) {
            return false;
        }

        return $this->evaluate($this->quietly);
    }
}
