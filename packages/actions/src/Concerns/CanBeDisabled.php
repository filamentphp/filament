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

        // Past `isHidden()` returning false, `isAuthorizedOrNotHiddenWhenUnauthorized()`
        // must have returned true — which means at least one of `hasAuthorizationTooltip()`,
        // `hasAuthorizationNotification()`, or `isAuthorized()` is true. The branches below
        // exploit that disjunction to decide without re-calling `isAuthorized()`.

        if ($this->hasAuthorizationNotification()) {
            return false;
        }

        if (! $this->hasAuthorizationTooltip()) {
            // `hasAuthorizationTooltip()` and `hasAuthorizationNotification()` are both
            // false, so `isAuthorized()` must be true, and this is not disabled.

            return false;
        }

        return ! $this->isAuthorized();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }

    public function hasDisabled(): bool
    {
        return $this->isDisabled !== false;
    }
}
