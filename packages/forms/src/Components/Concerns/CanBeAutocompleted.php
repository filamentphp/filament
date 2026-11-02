<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAutocompleted
{
    protected bool | string | Closure | null $autocomplete = null;

    public function autocomplete(bool | string | Closure | null $autocomplete = true): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function getAutocomplete(): ?string
    {
        return match ($autocomplete = $this->evaluate($this->autocomplete)) {
            true => 'on',
            false => 'off',
            default => $autocomplete,
        };
    }
}
