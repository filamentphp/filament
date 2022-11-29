<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Support\Arr;

trait HasMeta
{
    /**
     * @var array<string, mixed>
     */
    protected array $meta = [];

    /**
     * @param  mixed  $value
     */
    public function meta(string $key, $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    /**
     * @param  string | array<string> | null  $keys
     * @return mixed
     */
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

    /**
     * @param  string | array<string>  $keys
     */
    public function hasMeta(string | array $keys): bool
    {
        return Arr::has($this->meta, $keys);
    }
}
