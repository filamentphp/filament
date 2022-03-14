<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Illuminate\Support\Str;

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
        return $this->tooltip;
    }
}
