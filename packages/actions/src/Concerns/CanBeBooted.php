<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeBooted
{
    protected ?Closure $bootUsing = null;

    protected bool $isBooted = false;

    public function bootUsing(?Closure $callback): static
    {
        $this->bootUsing = $callback;

        return $this;
    }

    public function boot(): void
    {
        if ($this->isBooted) {
            return;
        }

        $this->isBooted = true;

        $this->evaluate($this->bootUsing);
    }
}
