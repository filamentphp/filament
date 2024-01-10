<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeHiddenResponsively
{
    protected string | Closure | null $hiddenFrom = null;

    protected string | Closure | null $visibleFrom = null;

    public function hiddenFrom(string | Closure | null $breakpoint): static
    {
        $this->hiddenFrom = $breakpoint;

        return $this;
    }

    public function visibleFrom(string | Closure | null $breakpoint): static
    {
        $this->visibleFrom = $breakpoint;

        return $this;
    }

    public function getHiddenFrom(): ?string
    {
        return $this->evaluate($this->hiddenFrom);
    }

    public function getVisibleFrom(): ?string
    {
        return $this->evaluate($this->visibleFrom);
    }
}
