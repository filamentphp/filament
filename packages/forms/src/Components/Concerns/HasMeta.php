<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Support\Arr;

trait HasMeta
{
    protected array $meta = [];

    public function meta(string $key, $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function getMeta(string | array | null $keys = null)
    {
        if (is_array($keys)) {
            return Arr::only($this->meta, $keys);
        }

        if (is_string($keys)) {
            return Arr::get($this->meta, $keys);
        }

        return $this->meta;
    }

    public function hasMeta(string | array $keys): bool
    {
        return Arr::has($this->meta, $keys);
    }
}
