<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutocapitalized
{
    protected $autocapitalize = null;

    public function autocapitalize(string | callable $autocapitalize = 'on'): static
    {
        $this->autocapitalize = $autocapitalize;

        return $this;
    }

    public function disableAutocapitalize(bool | callable $condition = true): static
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
