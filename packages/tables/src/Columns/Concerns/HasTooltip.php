<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasTooltip
{
    protected string | Closure | null $tooltip = null;

    protected string | Closure | null $emptyTooltip = null;

    public function tooltip(string | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(mixed $state = null): ?string
    {
        return $this->evaluate($this->tooltip, [
            'state' => $state,
        ]);
    }

    public function emptyTooltip(string | Closure | null $tooltip): static
    {
        $this->emptyTooltip = $tooltip;

        return $this;
    }

    public function getEmptyTooltip(): ?string
    {
        return $this->evaluate($this->emptyTooltip);
    }
}
