<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanClose
{
    protected bool | Closure $canClose = false;

    public function close(bool | Closure $condition = true): static
    {
        $this->canClose = $condition;

        return $this;
    }

    public function canClose(): bool
    {
        return (bool) $this->evaluate($this->canClose);
    }
}
