<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanDisablePlaceholderSelection
{
    protected bool | Closure | null $isPlaceholderSelectionDisabled = false;

    public function disablePlaceholderSelection(bool | Closure $condition = true): static
    {
        $this->isPlaceholderSelectionDisabled = $condition;

        return $this;
    }

    public function isPlaceholderSelectionDisabled(): bool
    {
        return (bool) $this->evaluate($this->isPlaceholderSelectionDisabled);
    }
}
