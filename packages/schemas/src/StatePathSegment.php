<?php

namespace Filament\Schemas;

/**
 * @internal Do not use this class outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
 */
class StatePathSegment
{
    public static function isSafe(string | int $segment): bool
    {
        if (is_int($segment)) {
            return true;
        }

        return preg_match('/\A[\p{L}\p{M}\p{N}_@+,=~-]+\z/u', $segment) === 1;
    }
}
