<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutocompleted
{
    protected $autocomplete = null;

    public function autocomplete(string | callable $autocomplete = 'on'): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function disableAutocomplete(bool | callable $condition = true): static
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
