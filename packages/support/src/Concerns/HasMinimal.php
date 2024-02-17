<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasMinimal
{
    protected bool | Closure $minimal = false;

    public function minimal(bool | Closure $minimal = true): static
    {
        $this->minimal = $minimal;

        return $this;
    }

    public function getMinimal(): bool
    {
        return (bool) $this->evaluate($this->minimal);
    }
}
