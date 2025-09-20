<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Arr;

class EnumArrayStateCast implements StateCast
{
    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public function __construct(
        protected string $enum,
    ) {}

    /**
     * @return array<BackedEnum>
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
            Arr::wrap($state),
            function (array $carry, $stateItem): array {
                if (blank($stateItem)) {
                    return $carry;
                }

                if ($stateItem instanceof BackedEnum) {
                    $carry[] = $stateItem;

                    return $carry;
                }

                $carry[] = $this->enum::tryFrom($stateItem);

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<mixed>
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
            Arr::wrap($state),
            function (array $carry, $stateItem): array {
                if (blank($stateItem)) {
                    return $carry;
                }

                if (! ($stateItem instanceof BackedEnum)) {
                    $carry[] = $stateItem;

                    return $carry;
                }

                $carry[] = strval($stateItem->value);

                return $carry;
            },
            initial: [],
        );
    }
}
