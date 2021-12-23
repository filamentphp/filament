<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait HasAction
{
    protected string | Closure | null $action = null;

    public function action(string | Closure | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string | Closure | null
    {
        return $this->action;
    }
}
