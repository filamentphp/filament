<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasTooltip
{
    protected string | Closure | null $tooltip = null;

    public function tooltip(string | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(): ?string
    {
        return $this->evaluate($this->tooltip);
    }
}
