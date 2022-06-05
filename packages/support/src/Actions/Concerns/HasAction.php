<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasAction
{
    protected Closure | string | null $action = null;

    public function action(Closure | string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): ?Closure
    {
        $action = $this->action;

        if (class_exists($action)) {
            $action = Closure::fromCallable($action);
        } elseif (is_string($action)) {
            $action = Closure::fromCallable([$this->getLivewire(), $action]);
        }

        return $action;
    }
}
