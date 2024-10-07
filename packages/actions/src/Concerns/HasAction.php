<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasAction
{
    protected Closure | string | null $action = null;

    protected bool | Closure | null $isLivewireClickHandlerEnabled = null;

    public function action(Closure | string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function livewireClickHandlerEnabled(bool | Closure | null $condition = true): static
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
        if (($isLivewireClickHandlerEnabled = $this->evaluate($this->isLivewireClickHandlerEnabled)) !== null) {
            return (bool) $isLivewireClickHandlerEnabled;
        }

        if (filled($this->getUrl())) {
            return false;
        }

        if ($this->canSubmitForm()) {
            return false;
        }

        return true;
    }

    public function hasAction(): bool
    {
        return $this->action !== null;
    }
}
