<?php

namespace Filament\Schemas\Components\StateCasts;

use Closure;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Str;

class NumberStateCast implements StateCast
{
    /**
     * @param  array<string>  $stripCharacters
     */
    public function __construct(
        protected bool $isNullable = true,
        protected bool $isInteger = false,
        protected array $stripCharacters = [],
        protected bool | Closure $isTrimmed = false,
    ) {}

    public function get(mixed $state): int | float | null
    {
        if (is_string($state)) {
            $state = str_replace($this->stripCharacters, '', $state);

            if (value($this->isTrimmed)) {
                $state = Str::trim($state);
            }
        }

        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $this->isInteger ? intval($state) : floatval($state);
    }

    public function set(mixed $state): int | float | null
    {
        if (is_string($state)) {
            $state = str_replace($this->stripCharacters, '', $state);
        }

        if ($this->isNullable && blank($state)) {
            return null;
        }

        return $this->isInteger ? intval($state) : floatval($state);
    }
}
