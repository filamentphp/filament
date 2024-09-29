<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\VerticalAlignment;

trait HasVerticalAlignment
{
    protected VerticalAlignment | string | Closure | null $verticalAlignment = null;

    public function verticalAlignment(VerticalAlignment | string | Closure | null $alignment): static
    {
        $this->verticalAlignment = $alignment;

        return $this;
    }

    public function verticallyAlignStart(bool | Closure $condition = true): static
    {
        return $this->verticalAlignment(fn (): ?VerticalAlignment => $this->evaluate($condition) ? VerticalAlignment::Start : null);
    }

    public function verticallyAlignCenter(bool | Closure $condition = true): static
    {
        return $this->verticalAlignment(fn (): ?VerticalAlignment => $this->evaluate($condition) ? VerticalAlignment::Center : null);
    }

    public function verticallyAlignEnd(bool | Closure $condition = true): static
    {
        return $this->verticalAlignment(fn (): ?VerticalAlignment => $this->evaluate($condition) ? VerticalAlignment::End : null);
    }

    public function getVerticalAlignment(): VerticalAlignment | string | null
    {
        $alignment = $this->evaluate($this->verticalAlignment);

        if (! is_string($alignment)) {
            return $alignment;
        }

        return VerticalAlignment::tryFrom($alignment) ?? $alignment;
    }
}
