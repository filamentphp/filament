<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\ActionGroup;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    protected ?bool $isHiddenCache = null;

    protected ?bool $isHiddenInGroupCache = null;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;
        $this->flushHiddenCache();

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;
        $this->flushHiddenCache();

        return $this;
    }

    public function flushHiddenCache(): void
    {
        $this->isHiddenCache = null;
        $this->isHiddenInGroupCache = null;
    }

    public function isHidden(): bool
    {
        if ($this->isHiddenCache !== null) {
            return $this->isHiddenCache;
        }

        if ($this->getGroup()?->baseIsHidden()) {
            return $this->isHiddenCache = true;
        }

        return $this->isHiddenCache = $this->isHiddenInGroup();
    }

    public function isHiddenInGroup(): bool
    {
        if ($this->isHiddenInGroupCache !== null) {
            return $this->isHiddenInGroupCache;
        }

        if ($this->evaluate($this->isHidden)) {
            return $this->isHiddenInGroupCache = true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return $this->isHiddenInGroupCache = true;
        }

        if ($this instanceof ActionGroup) {
            foreach ($this->getActions() as $action) {
                if (! $action->isHiddenInGroup()) {
                    return $this->isHiddenInGroupCache = false;
                }
            }

            return $this->isHiddenInGroupCache = true;
        }

        return $this->isHiddenInGroupCache = (! $this->isAuthorizedOrNotHiddenWhenUnauthorized());
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
