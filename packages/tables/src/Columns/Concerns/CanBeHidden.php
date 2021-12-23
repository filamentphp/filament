<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeHidden
{
    protected ?string $hiddenFrom = null;

    protected bool | Closure $isHidden = false;

    protected ?string $visibleFrom = null;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function hiddenFrom(string $breakpoint): static
    {
        $this->hiddenFrom = $breakpoint;

        return $this;
    }

    public function visibleFrom(string $breakpoint): static
    {
        $this->visibleFrom = $breakpoint;

        return $this;
    }

    public function getHiddenFrom(): ?string
    {
        return $this->hiddenFrom;
    }

    public function getVisibleFrom(): ?string
    {
        return $this->visibleFrom;
    }

    public function isHidden(): bool
    {
        return $this->evaluate($this->isHidden);
    }
}
