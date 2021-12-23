<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAutocapitalized
{
    protected string | Closure | null $autocapitalize = null;

    public function autocapitalize(string | Closure | null $autocapitalize = 'on'): static
    {
        $this->autocapitalize = $autocapitalize;

        return $this;
    }

    public function disableAutocapitalize(bool | Closure $condition = true): static
    {
        $this->autocapitalize(function () use ($condition): ?string {
            return $this->evaluate($condition) ? 'off' : null;
        });

        return $this;
    }

    public function getAutocapitalize(): ?string
    {
        return $this->evaluate($this->autocapitalize);
    }
}
