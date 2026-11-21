<?php

namespace Filament\Support;

use Illuminate\Support\Str;

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

        if (blank($segment)) {
            return false;
        }

        if (preg_match('/[<>"\'`\x00-\x1F\x7F]/', $segment)) {
            return false;
        }

        if (Str::contains($segment, ['.', '*']) || str_ends_with($segment, '\\')) {
            return false;
        }

        return ! in_array($segment, ['{first}', '{last}', '\\{first}', '\\{last}'], strict: true);
    }
}
