<?php

namespace Filament\Forms\Components\Concerns;

trait HasLabel
{
    protected $label = null;

    public function label(string | callable $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }
}
