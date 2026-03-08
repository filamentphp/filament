<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class StripCharactersStateCast implements StateCast
{
    /**
     * @param  array<string>  $characters
     */
    public function __construct(
        protected array $characters = [],
    ) {}

    public function get(mixed $state): mixed
    {
        if (empty($this->characters)) {
            return $state;
        }

        if (is_array($state)) {
            return array_map(fn (mixed $value): mixed => is_string($value) ? str_replace($this->characters, '', $value) : $value, $state);
        }

        if (! is_string($state)) {
            return $state;
        }

        return str_replace($this->characters, '', $state);
    }

    public function set(mixed $state): mixed
    {
        if (empty($this->characters)) {
            return $state;
        }

        if (is_array($state)) {
            return array_map(fn (mixed $value): mixed => is_string($value) ? str_replace($this->characters, '', $value) : $value, $state);
        }

        if (! is_string($state)) {
            return $state;
        }

        return str_replace($this->characters, '', $state);
    }
}
