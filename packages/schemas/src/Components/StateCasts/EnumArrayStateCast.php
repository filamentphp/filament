<?php

namespace Filament\Schemas\Components\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Arr;

class EnumArrayStateCast implements StateCast
{
    protected EnumStateCast $enumStateCast;

    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public function __construct(
        protected string $enum,
    ) {
        $this->enumStateCast = app(EnumStateCast::class, ['enum' => $enum]);
    }

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

        /** @var array<mixed> $state */
        $state = Arr::wrap($state);

        return array_reduce(
            $state,
            function (array $carry, $stateItem): array {
                $stateItem = $this->enumStateCast->get($stateItem);

                if ($stateItem === null) {
                    return $carry;
                }

                $carry[] = $stateItem;

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

        /** @var array<mixed> $state */
        $state = Arr::wrap($state);

        return array_reduce(
            $state,
            function (array $carry, $stateItem): array {
                $stateItem = $this->enumStateCast->set($stateItem);

                if ($stateItem === null) {
                    return $carry;
                }

                $carry[] = $stateItem;

                return $carry;
            },
            initial: [],
        );
    }
}
