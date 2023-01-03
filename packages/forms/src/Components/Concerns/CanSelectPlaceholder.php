<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Components\ViewComponent;

trait CanSelectPlaceholder
{
    protected bool | Closure | null $canSelectPlaceholder = true;

    public function selectablePlaceholder(bool | Closure $condition = true): static
    {
        $this->canSelectPlaceholder = $condition;

        return $this;
    }

    /**
     * @deprecated Use `selectablePlaceholder()` instead.
     */
    public function disablePlaceholderSelection(bool | Closure $condition = true): static
    {
        $this->selectablePlaceholder(fn (): bool => ! $this->evaluate($condition));

        return $this;
    }

    public function isPlaceholderSelectionDisabled(): bool
    {
        return (bool) $this->evaluate($this->canSelectPlaceholder);
    }
}
