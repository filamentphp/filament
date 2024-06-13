<?php

namespace Filament\Schema\Components\StateCasts;

use BackedEnum;
use Filament\Schema\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Arr;

class KeyValueStateCast implements StateCast
{
    public function get(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        if (! is_array(Arr::first($state))) {
            return $state;
        }

        return Arr::pluck($state, 'value', 'key');
    }

    public function set(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        return array_map(
            fn ($value, $key): array => ['key' => $key, 'value', $value],
            $state,
            array_keys($state),
        );
    }
}
