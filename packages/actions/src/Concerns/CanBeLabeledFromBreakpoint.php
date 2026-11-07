<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeLabeledFromBreakpoint
{
    protected string | Closure | null $labeledFromBreakpoint = null;

    public function labeledFrom(string | Closure | null $breakpoint = null): static
    {
        $this->labeledFromBreakpoint = $breakpoint;

        return $this;
    }

    public function getLabeledFromBreakpoint(): ?string
    {
        return $this->evaluate($this->labeledFromBreakpoint);
    }

    public function hasLabeledFromBreakpoint(): bool
    {
        return $this->labeledFromBreakpoint !== null;
    }
}
