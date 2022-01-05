<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

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

        if (is_string($action)) {
            $action = fn (...$args) => $this->getLivewire()->{$action}(...$args);
        }

        return $action;
    }
}
