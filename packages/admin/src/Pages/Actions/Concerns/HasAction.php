<?php

namespace Filament\Pages\Actions\Concerns;

use Closure;

trait HasAction
{
    protected Closure | string | null $action = null;

    public function action(Closure | string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): Closure | string | null
    {
        return $this->action;
    }
}
