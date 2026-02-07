<?php

namespace Filament\Pages;

class PageConfiguration
{
    protected ?string $slug = null;

    /**
     * @param  class-string<Page>  $page
     */
    public function __construct(
        public readonly string $page,
        public readonly string $key,
    ) {}

    /**
     * @param  class-string<Page>  $page
     */
    public static function make(string $page, string $key): static
    {
        return app(static::class, ['page' => $page, 'key' => $key]);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return class-string<Page>
     */
    public function getPage(): string
    {
        return $this->page;
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
