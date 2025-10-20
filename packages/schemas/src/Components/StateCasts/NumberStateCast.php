<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class NumberStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    public function get(mixed $state): ?float
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return floatval($state);
    }

    public function set(mixed $state): ?float
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return floatval($state);
    }
}
