<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Support\Str;

trait CanTrimState
{
    protected bool | Closure $isTrimmed = false;

    protected ?bool $cachedIsTrimmed = null;

    public function trim(bool | Closure $condition = true): static
    {
        $this->isTrimmed = $condition;

        $this->cachedIsTrimmed = null;

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

        return Str::trim($state);
    }
}
