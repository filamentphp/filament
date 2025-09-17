<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait CanTrimState
{
    protected bool | Closure $isTrimmed = false;

    protected bool $cachedIsTrimmed;

    public function trim(bool | Closure $condition = true): static
    {
        $this->isTrimmed = $condition;

        return $this;
    }

    public function isTrimmed(): bool
    {
        return $this->cachedIsTrimmed ??= (bool) $this->evaluate($this->isTrimmed);
    }

    protected function trimState(mixed $state): mixed
    {
        if (! is_string($state)) {
            return $state;
        }

        if (! $this->isTrimmed()) {
            return $state;
        }

        return trim($state);
    }
}
