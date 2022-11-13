<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasGroup
{
    protected string | Closure | null $group = null;

    public function group(string | Closure | null $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->evaluate($this->group);
    }
}
