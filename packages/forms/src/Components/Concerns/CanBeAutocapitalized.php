<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Field;

trait CanBeAutocapitalized
{
    protected bool | string | Closure | null $autocapitalize = null;

    public function autocapitalize(bool | string | Closure | null $autocapitalize = true): static
    {
        $this->autocapitalize = $autocapitalize;

        return $this;
    }

    /**
     * @deprecated Use `autocapitalize()` instead.
     */
    public function disableAutocapitalize(bool | Closure $condition = true): static
    {
        $this->autocapitalize(static function (Field $component) use ($condition): ?bool {
            return $component->evaluate($condition) ? false : null;
        });

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
