<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasHint
{
    protected string | Closure | null $hint = null;

    public function hint(string | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->evaluate($this->hint);
    }
}
