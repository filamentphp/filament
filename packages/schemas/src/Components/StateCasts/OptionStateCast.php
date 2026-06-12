<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class OptionStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    public function get(mixed $state): string | int | null
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        if (
            is_int($state)
            || (
                is_string($state)
                && ctype_digit($state)
                && (($state === '0') || (! str($state)->startsWith('0')))
            )
        ) {
            $max = (string) PHP_INT_MAX;

            if (
                (strlen($state) > strlen($max)) ||
                ((strlen($state) === strlen($max)) && (strcmp($state, $max) > 0))
            ) {
                return strval($state);
            }

            return intval($state);
        }

        return strval($state);
    }

    public function set(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        return strval($state);
    }
}
