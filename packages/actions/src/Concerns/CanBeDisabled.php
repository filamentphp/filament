<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function isDisabled(): bool
    {
        if ($this->evaluate($this->isDisabled)) {
            return true;
        }

        if ($this->isHidden()) {
            return true;
        }

        if ($this->hasAuthorizationNotification()) {
            return false;
        }

        return ! $this->isAuthorized();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
