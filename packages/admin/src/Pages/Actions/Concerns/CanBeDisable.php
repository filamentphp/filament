<?php

namespace Filament\Pages\Actions\Concerns;

use Closure;

trait CanBeDisable
{
    protected bool | Closure $isDisabled = false;

    public function disable(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }
}
