<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasAction
{
    protected Closure | string | null $action = null;

    protected bool | Closure $isLivewireClickHandlerEnabled = true;

    public function action(Closure | string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function livewireClickHandlerEnabled(bool | Closure $condition = true): static
    {
        $this->isLivewireClickHandlerEnabled = $condition;

        return $this;
    }

    public function getActionFunction(): ?Closure
    {
        if (! $this->action instanceof Closure) {
            return null;
        }

        return $this->action;
    }

    public function isLivewireClickHandlerEnabled(): bool
    {
        if (! $this->evaluate($this->isLivewireClickHandlerEnabled)) {
            return false;
        }

        if (filled($this->getUrl())) {
            return false;
        }

        if ($this->canSubmitForm()) {
            return false;
        }

        return true;
    }
}
