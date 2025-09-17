<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasBadgeTooltip
{
    protected string | Closure | null $badgeTooltip = null;

    public function badgeTooltip(string | Closure | null $tooltip): static
    {
        $this->badgeTooltip = $tooltip;

        return $this;
    }

    public function getBadgeTooltip(): ?string
    {
        return $this->evaluate($this->badgeTooltip);
    }
}
