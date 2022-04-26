<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Field;

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
        $this->autocomplete(static function (Field $component) use ($condition): ?string {
            return $component->evaluate($condition) ? 'off' : null;
        });

        return $this;
    }

    public function getAutocomplete(): ?string
    {
        return $this->evaluate($this->autocomplete);
    }
}
