<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Uri;

class UriStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    /**
     * @param  string|null  $state
     */
    public function get(mixed $state): ?Uri
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return Uri::of($state);
    }

    /**
     * @param  Uri|null  $state
     */
    public function set(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $state->__toString();
    }
}
