<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Field;

trait CanBeAutocompleted
{
    protected bool | string | Closure | null $autocomplete = null;

    public function autocomplete(bool | string | Closure | null $autocomplete = true): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    /**
     * @deprecated Use `autocomplete()` instead.
     */
    public function disableAutocomplete(bool | Closure $condition = true): static
    {
        $this->autocomplete(static function (Field $component) use ($condition): ?bool {
            return $component->evaluate($condition) ? false : null;
        });

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
