<?php

namespace Filament\Resources;

class ResourceConfiguration
{
    protected ?string $slug = null;

    /**
     * @param  class-string  $resource
     */
    public function __construct(
        public readonly string $resource,
        public readonly string $key,
    ) {}

    /**
     * @param  class-string  $resource
     */
    public static function make(string $resource, string $key): static
    {
        return app(static::class, ['resource' => $resource, 'key' => $key]);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return class-string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    public function slug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
