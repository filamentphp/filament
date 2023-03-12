<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasIndicator
{
    protected string | Closure | null $indicator = null;

    protected string | Closure | null $indicatorColor = null;

    public function indicator(string | Closure | null $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function indicatorColor(string | Closure | null $color): static
    {
        $this->indicatorColor = $color;

        return $this;
    }

    public function getIndicator(): ?string
    {
        return $this->evaluate($this->indicator);
    }

    public function getIndicatorColor(): ?string
    {
        return $this->evaluate($this->indicatorColor);
    }
}
