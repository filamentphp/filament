<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class StringStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    public function get(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return strval($state);
    }

    public function set(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return strval($state);
    }
}
