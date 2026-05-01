<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasTooltip
{
    protected string | Htmlable | Closure | null $tooltip = null;

    protected string | Htmlable | Closure | null $emptyTooltip = null;

    protected bool $shouldTranslateTooltip = false;

    protected bool $shouldTranslateEmptyTooltip = false;

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

    public function getTooltip(mixed $state = null): string | Htmlable | null
    {
        $tooltip = $this->evaluate($this->tooltip, [
            'state' => $state,
        ]);

        return (is_string($tooltip) && $this->shouldTranslateTooltip) ?
            __($tooltip) :
            $tooltip;
    }

    public function emptyTooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->emptyTooltip = $tooltip;

        return $this;
    }

    public function translateEmptyTooltip(bool $shouldTranslateEmptyTooltip = true): static
    {
        $this->shouldTranslateEmptyTooltip = $shouldTranslateEmptyTooltip;

        return $this;
    }

    public function getEmptyTooltip(): string | Htmlable | null
    {
        $tooltip = $this->evaluate($this->emptyTooltip);

        return (is_string($tooltip) && $this->shouldTranslateEmptyTooltip) ?
            __($tooltip) :
            $tooltip;
    }
}
