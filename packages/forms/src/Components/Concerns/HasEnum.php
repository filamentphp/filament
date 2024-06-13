<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasEnum
{
    protected string | Closure | null $enum = null;

    public function enum(string | Closure | null $enum): static
    {
        $this->enum = $enum;

        return $this;
    }

    public function getEnum(): ?string
    {
        return $this->evaluate($this->enum);
    }
}
