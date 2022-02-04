<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeHidden
{
    protected string | Closure | null $hiddenFrom = null;

    protected bool | Closure $isHidden = false;

    protected string | Closure | null $visibleFrom = null;

    protected bool | Closure $isVisible = true;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

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

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        return ! $this->evaluate($this->isVisible);
    }
}
