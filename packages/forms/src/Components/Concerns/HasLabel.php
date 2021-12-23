<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasLabel
{
    protected bool | Closure $isLabelHidden = false;

    protected string | Closure | null $label = null;

    public function disableLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    public function isLabelHidden(): bool
    {
        return $this->evaluate($this->isLabelHidden);
    }
}
