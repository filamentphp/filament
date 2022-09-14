<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasToggleColors
{
    protected string | Closure | null $offColor = null;

    protected string | Closure | null $onColor = null;

    public function offColor(string | Closure | null $color): static
    {
        $this->offColor = $color;

        return $this;
    }

    public function onColor(string | Closure | null $color): static
    {
        $this->onColor = $color;

        return $this;
    }

    public function getOffColor(): ?string
    {
        return $this->evaluate($this->offColor);
    }

    public function getOnColor(): ?string
    {
        return $this->evaluate($this->onColor);
    }
}
