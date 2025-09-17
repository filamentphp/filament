<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FileUploadStateCast implements StateCast
{
    public function __construct(
        protected bool $isMultiple = false,
    ) {}

    public function get(mixed $state): mixed
    {
        if ($this->isMultiple) {
            return array_values(Arr::wrap($state));
        }

        if (! is_array($state)) {
            return $state;
        }

        return Arr::first($state);
    }

    public function set(mixed $state): mixed
    {
        $newState = [];

        foreach (Arr::wrap($state) as $key => $file) {
            if (blank($file)) {
                continue;
            }

            if (is_numeric($key)) {
                $key = (string) Str::uuid();
            }

            $newState[$key] = $file;
        }

        return $newState;
    }
}
