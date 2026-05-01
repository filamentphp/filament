<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasTooltip
{
    protected string | Htmlable | Closure | null $tooltip = null;

    protected bool $shouldTranslateTooltip = false;

    public function tooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function translateTooltip(bool $shouldTranslateTooltip = true): static
    {
        $this->shouldTranslateTooltip = $shouldTranslateTooltip;

        return $this;
    }

    public function getTooltip(): string | Htmlable | null
    {
        $tooltip = $this->evaluate($this->tooltip);

        return (is_string($tooltip) && $this->shouldTranslateTooltip) ?
            __($tooltip) :
            $tooltip;
    }
}
