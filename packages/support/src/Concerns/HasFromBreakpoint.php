<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasFromBreakpoint
{
    protected string | Closure | null $fromBreakpoint = null;

    public function from(string | Closure | null $breakpoint): static
    {
        $this->fromBreakpoint = $breakpoint;

        return $this;
    }

    public function getFromBreakpoint(): ?string
    {
        return $this->evaluate($this->fromBreakpoint);
    }
}
