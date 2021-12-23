<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAutocompleted
{
    protected string | Closure | null $autocomplete = null;

    public function autocomplete(string | Closure | null $autocomplete = 'on'): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function disableAutocomplete(bool | Closure $condition = true): static
    {
        $this->autocomplete(function () use ($condition): ?string {
            return $this->evaluate($condition) ? 'off' : null;
        });

        return $this;
    }

    public function getAutocomplete(): ?string
    {
        return $this->evaluate($this->autocomplete);
    }
}
