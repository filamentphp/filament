<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class GlobalSearchResults
{
    /**
     * @var Collection<string, array<GlobalSearchResult> | Arrayable<array-key, GlobalSearchResult>>
     */
    protected Collection $categories;

    final public function __construct()
    {
        $this->categories = Collection::make();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @param  array<GlobalSearchResult> | Arrayable<array-key, GlobalSearchResult>  $results
     */
    public function category(string $name, array | Arrayable $results = []): static
    {
        $this->categories[$name] = $results;

        return $this;
    }

    /**
     * @return Collection<string, array<GlobalSearchResult> | Arrayable<array-key, GlobalSearchResult>>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
}
