<?php

namespace Filament\Forms\Components\Concerns;

trait HasLabel
{
    protected $isLabelHidden = false;

    protected $label = null;

    public function disableLabel(bool | callable $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | callable $label): static
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
