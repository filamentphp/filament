<?php

namespace Filament\Schemas\Components\StateCasts;

use Closure;
use Filament\Schemas\Components\StateCasts\Contracts\ReadOnlyStateCast;
use Illuminate\Support\Str;

class TrimStateCast implements ReadOnlyStateCast
{
    public function __construct(
        protected bool | Closure $isTrimmed = true,
        protected bool $isArray = false,
    ) {}

    public function get(mixed $state): mixed
    {
        if ($this->isArray) {
            // Preserve serialized tags until `hydrateTags()` has split them by their separator.
            return is_array($state) ? array_map($this->trimState(...), $state) : $state;
        }

        return $this->trimState($state);
    }

    public function set(mixed $state): mixed
    {
        return $state;
    }

    protected function trimState(mixed $state): mixed
    {
        if (! is_string($state)) {
            return $state;
        }

        if (! value($this->isTrimmed)) {
            return $state;
        }

        return Str::trim($state);
    }
}
