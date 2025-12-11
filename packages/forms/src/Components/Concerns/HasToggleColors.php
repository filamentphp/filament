<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasToggleColors
{
    protected string | array | Closure | null $offColor = null;

    protected string | array | Closure | null $onColor = null;

    public function offColor(string | array | Closure | null $color): static
    {
        $this->offColor = $color;

        return $this;
    }

    public function onColor(string | array | Closure | null $color): static
    {
        $this->onColor = $color;

        return $this;
    }

    public function getOffColor(): array | string | null
    {
        return $this->evaluate($this->offColor);
    }

    public function getOnColor(): array | string | null
    {
        return $this->evaluate($this->onColor);
    }
}
