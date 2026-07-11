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

        // Security: this cast backs single-value option fields, so a legitimate state is
        // always a scalar or `null`. A tampered request payload can deliver an array (or
        // other non-scalar), which would throw a `TypeError` at the `strval()` below and
        // crash the request. Fail closed by treating it as no selection instead.
        if (! is_scalar($state) && (! $state instanceof BackedEnum)) {
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

        // Security: mirror `get()` and fail closed on a tampered non-scalar value so a
        // malformed array cannot reach `strval()` and crash the request.
        if (! is_scalar($state) && (! $state instanceof BackedEnum)) {
            return null;
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        return strval($state);
    }
}
