<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Support\Str;

trait CanTrimState
{
    protected bool | Closure $isTrimmed = false;

    protected ?bool $cachedIsTrimmed = null;

    protected bool $isEvaluatingTrimmed = false;

    public function trim(bool | Closure $condition = true): static
    {
        $this->isTrimmed = $condition;

        $this->cachedIsTrimmed = null;

        return $this;
    }

    public function isTrimmed(): bool
    {
        // A condition may read this field's state before its trimming decision is known.
        if ($this->isEvaluatingTrimmed) {
            return false;
        }

        if ($this->cachedIsTrimmed !== null) {
            return $this->cachedIsTrimmed;
        }

        $this->isEvaluatingTrimmed = true;

        try {
            return $this->cachedIsTrimmed = (bool) $this->evaluate($this->isTrimmed);
        } finally {
            $this->isEvaluatingTrimmed = false;
        }
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
