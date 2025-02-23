<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\ActionGroup;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        if ($this->getGroup()?->baseIsHidden()) {
            return true;
        }

        return $this->isHiddenInGroup();
    }

    public function isHiddenInGroup(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return true;
        }

        if ($this instanceof ActionGroup) {
            return false;
        }

        return ! $this->isAuthorizedOrNotHiddenWhenUnauthorized();
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
