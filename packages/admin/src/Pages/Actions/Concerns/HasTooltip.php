<?php

namespace Filament\Pages\Actions\Concerns;

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
        return value($this->tooltip);
    }
}
