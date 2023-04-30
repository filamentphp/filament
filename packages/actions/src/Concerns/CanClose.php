<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanClose
{
    protected bool | Closure $shouldClose = false;

    public function close(bool | Closure $condition = true): static
    {
        $this->shouldClose = $condition;

        return $this;
    }

    public function shouldClose(): bool
    {
        return (bool) $this->evaluate($this->shouldClose);
    }
}
