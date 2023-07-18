<?php

namespace Filament\Infolists\Components\Concerns;

use Illuminate\Support\Arr;

trait HasMeta
{
    /**
     * @var array<string, mixed>
     */
    protected array $meta = [];

    public function meta(string $key, mixed $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    /**
     * @param  string | array<string> | null  $keys
     */
    public function getMeta(string | array | null $keys = null): mixed
    {
        if (is_array($keys)) {
            return Arr::only($this->meta, $keys);
        }

        if (is_string($keys)) {
            return Arr::get($this->meta, $keys);
        }

        return $this->meta;
    }

    /**
     * @param  string | array<string>  $keys
     */
    public function hasMeta(string | array $keys): bool
    {
        return Arr::has($this->meta, $keys);
    }
}
