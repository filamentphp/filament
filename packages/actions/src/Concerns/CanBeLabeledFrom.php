<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeLabeledFrom
{
    protected string | Closure | null $labeledFrom = null;

    public function labeledFrom(string | Closure | null $breakpoint = null): static
    {
        $this->labeledFrom = $breakpoint;

        return $this;
    }

    public function getLabeledFromBreakpoint(): ?string
    {
        return $this->evaluate($this->labeledFrom);
    }
}
