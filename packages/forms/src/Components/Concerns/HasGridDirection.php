<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Enums\GridDirection;

trait HasGridDirection
{
    protected GridDirection | string | Closure | null $gridDirection = null;

    public function gridDirection(GridDirection | string | Closure | null $gridDirection): static
    {
        $this->gridDirection = $gridDirection;

        return $this;
    }

    public function getGridDirection(): ?GridDirection
    {
        $direction = $this->evaluate($this->gridDirection);

        if (filled($direction) && (! ($direction instanceof GridDirection))) {
            $direction = GridDirection::tryFrom($direction);
        }

        return $direction;
    }
}
