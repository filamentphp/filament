<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use UnitEnum;

trait HasEnum
{
    protected string | Closure | null $enum = null;

    public function enum(string | Closure | null $enum): static
    {
        $this->enum = $enum;

        return $this;
    }

    /**
     * @return ?class-string<UnitEnum>
     */
    public function getEnum(): ?string
    {
        return $this->evaluate($this->enum);
    }
}
