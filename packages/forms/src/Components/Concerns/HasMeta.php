<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Support\Arr;

trait HasMeta
{
    protected array $meta = [];

    public function getMeta(string|array $keys = null): mixed
    {
        if ($keys !== null) {
            return Arr::only($this->meta, $keys);
        }

        return $this->meta;
    }

    public function hasMeta(string|array $keys): bool
    {
        return Arr::has($this->meta, $keys);
    }

    public function meta(string $key, mixed $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }
}
