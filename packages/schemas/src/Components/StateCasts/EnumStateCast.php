<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Stringable;

class EnumStateCast implements StateCast
{
    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public function __construct(
        protected string $enum,
    ) {}

    public function get(mixed $state): ?BackedEnum
    {
        if (blank($state)) {
            return null;
        }

        if ($state instanceof $this->enum) {
            return $state;
        }

        if ((! is_scalar($state)) && (! $state instanceof Stringable)) {
            return null;
        }

        if ($state instanceof Stringable) {
            $state = (string) $state;
        }

        return $this->enum::tryFrom($state);
    }

    public function set(mixed $state): mixed
    {
        if (! ($state instanceof BackedEnum)) {
            return $state;
        }

        return strval($state->value);
    }
}
