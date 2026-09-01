<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Arr;

class KeyValueStateCast implements StateCast
{
    /**
     * @return array<mixed, mixed>
     */
    public function get(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        if (! is_array($state[array_key_first($state)])) {
            return $state;
        }

        $state = Arr::pluck($state, 'value', 'key');

        if (array_key_exists('', $state)) {
            unset($state['']);
        }

        return $state;
    }

    /**
     * @return array<array{key: mixed, value: mixed}>
     */
    public function set(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        if (is_array($state[array_key_first($state)])) {
            return $state;
        }

        if (array_key_exists('', $state)) {
            unset($state['']);
        }

        return array_map(
            fn ($value, $key): array => ['key' => $key, 'value' => $value],
            $state,
            array_keys($state),
        );
    }
}
