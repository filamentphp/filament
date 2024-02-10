<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasLineClamp
{
    protected int | Closure | null $lineClamp = null;

    public function lineClamp(int | Closure | null $lineClamp): static
    {
        $this->lineClamp = $lineClamp;

        return $this;
    }

    public function getLineClamp(): ?int
    {
        return $this->evaluate($this->lineClamp);
    }
}
