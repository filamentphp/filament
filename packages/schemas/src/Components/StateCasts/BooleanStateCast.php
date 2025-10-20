<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class BooleanStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
        protected bool $isStoredAsInt = false,
    ) {}

    public function get(mixed $state): ?bool
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return boolval($state);
    }

    public function set(mixed $state): bool | int | null
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        if ($this->isStoredAsInt) {
            return $state ? 1 : 0;
        }

        return boolval($state);
    }
}
