<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class StringArrayStateCast implements StateCast
{
    /**
     * @return array<string>
     */
    public function get(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        return array_reduce(
            $state,
            function (array $carry, $stateItem): array {
                if (blank($stateItem)) {
                    return $carry;
                }

                if ($stateItem instanceof BackedEnum) {
                    $stateItem = $stateItem->value;
                }

                $carry[] = strval($stateItem);

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<string>
     */
    public function set(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        return array_reduce(
            $state,
            function (array $carry, $stateItem): array {
                if (blank($stateItem)) {
                    return $carry;
                }

                if ($stateItem instanceof BackedEnum) {
                    $stateItem = $stateItem->value;
                }

                $carry[] = strval($stateItem);

                return $carry;
            },
            initial: [],
        );
    }
}
