<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class NumberStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
        protected bool $isInteger = false,
    ) {}

    public function get(mixed $state): int | float | null
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $this->isInteger ? intval($state) : floatval($state);
    }

    public function set(mixed $state): int | float | null
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $this->isInteger ? intval($state) : floatval($state);
    }
}
