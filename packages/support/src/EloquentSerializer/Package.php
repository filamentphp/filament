<?php

namespace Filament\Support\EloquentSerializer;

class Package
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        private array $data,
    ) {}

    public function get(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->data;
        }

        return $this->data[$key] ?? null;
    }
}
