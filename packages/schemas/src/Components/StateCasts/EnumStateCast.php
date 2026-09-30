<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Stringable;
use TypeError;

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

        if ($state instanceof Stringable) {
            $state = (string) $state;
        }

        if ((! is_int($state)) && (! is_string($state))) {
            return null;
        }

        try {
            return $this->enum::tryFrom($state);
        } catch (TypeError) {
            return null;
        }
    }

    public function set(mixed $state): mixed
    {
        $state = $this->get($state);

        if ($state === null) {
            return null;
        }

        return strval($state->value);
    }
}
