<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasAction
{
    protected Closure | string | null $action = null;

    protected bool | Closure $isMountedOnClick = true;

    public function action(Closure | string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function mountedOnClick(bool | Closure $condition = true): static
    {
        $this->isMountedOnClick = $condition;

        return $this;
    }

    public function getActionFunction(): ?Closure
    {
        if (! $this->action instanceof Closure) {
            return null;
        }

        return $this->action;
    }

    public function isMountedOnClick(): bool
    {
        return (bool) $this->evaluate($this->isMountedOnClick);
    }
}
