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
        return $this->evaluate($this->placeholder);
    }
}
