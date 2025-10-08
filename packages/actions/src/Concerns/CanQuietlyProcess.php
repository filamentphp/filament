<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanQuietlyProcess
{
    protected ?Closure $quietly = null;

    /**
     * Set a condition to determine if a operation should be quiet
     * @param Closure(static): bool $quietly
     */
    public function quietly(Closure $quietly): static
    {
        $this->quietly = $quietly;

        return $this;
    }

    protected function shouldQuietlyProcess(): bool
    {
        if (! $this->quietly) {
            return false;
        }

        return (bool) $this->evaluate($this->quietly);
    }
}
