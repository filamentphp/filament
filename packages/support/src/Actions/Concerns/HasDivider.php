<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasDivider
{
    protected bool | Closure $hasDividerBefore = false;

    protected bool | Closure $hasDividerAfter = false;

    public function dividerBefore(bool | Closure $condition = true): static
    {
        $this->hasDividerBefore = $condition;

        return $this;
    }

    public function dividerAfter(bool | Closure $condition = true): static
    {
        $this->hasDividerAfter = $condition;

        return $this;
    }

    public function hasDividerBefore(): bool
    {
        return $this->evaluate($this->hasDividerBefore);
    }

    public function hasDividerAfter(): bool
    {
        return $this->evaluate($this->hasDividerAfter);
    }
}
