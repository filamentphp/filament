<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasPlaceholder
{
    protected string | Closure | null $placeholder = null;

    public function placeholder(string | Closure | null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder?$this->evaluate($this->placeholder):__('filament-forms::components.text_input.enter') .' '.$this->getLabel();
    }
}
