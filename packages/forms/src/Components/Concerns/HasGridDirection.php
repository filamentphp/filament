<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasGridDirection
{
    protected string | Closure | null $gridDirection = null;

    public function gridDirection(string | Closure | null $gridDirection): static
    {
        $this->gridDirection = $gridDirection;

        return $this;
    }

    public function getGridDirection(): ?string
    {
        return $this->evaluate($this->gridDirection);
    }
}
