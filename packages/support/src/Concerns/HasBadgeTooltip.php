<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasBadgeTooltip
{
    protected string | Htmlable | Closure | null $badgeTooltip = null;

    public function badgeTooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->badgeTooltip = $tooltip;

        return $this;
    }

    public function getBadgeTooltip(): string | Htmlable | null
    {
        return $this->evaluate($this->badgeTooltip);
    }
}
