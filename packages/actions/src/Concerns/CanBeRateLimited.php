<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeRateLimited
{
    protected int | Closure | null $rateLimit = null;

    public function rateLimit(int | Closure | null $maxAttempts): static
    {
        $this->rateLimit = $maxAttempts;

        return $this;
    }

    public function getRateLimit(): ?int
    {
        return $this->evaluate($this->rateLimit);
    }
}
