<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAutocapitalized
{
    protected bool | string | Closure | null $autocapitalize = null;

    public function autocapitalize(bool | string | Closure | null $autocapitalize = true): static
    {
        $this->autocapitalize = $autocapitalize;

        return $this;
    }

    public function getAutocapitalize(): ?string
    {
        return match ($autocapitalize = $this->evaluate($this->autocapitalize)) {
            true => 'on',
            false => 'off',
            default => $autocapitalize,
        };
    }
}
