<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class NumberStateCast implements StateCast
{
    /**
     * @param  array<string>  $stripCharacters
     */
    public function __construct(
        protected bool $isNullable = true,
        protected bool $isInteger = false,
        protected array $stripCharacters = [],
    ) {}

    public function get(mixed $state): int | float | null
    {
        if (is_string($state)) {
            $state = str_replace($this->stripCharacters, '', $state);
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
