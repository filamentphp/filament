<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class GlobalSearchResults
{
    protected Collection $categories;

    protected Collection $sorting;

    final public function __construct()
    {
        $this->categories = Collection::make();
        $this->sorting = Collection::make();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @param  array<GlobalSearchResult> | Arrayable  $results
     * @return GlobalSearchResults
     */
    public function category(string $name, array | Arrayable $results = [], ?int $sort = null): static
    {
        $this->categories->put($name, $results);
        $this->sorting->put($name, $sort);

        return $this;
    }

    public function getCategories(): Collection
    {
        $index = 0;

        return $this->categories->sortBy(function ($value, $key) use (&$index) {
            $index++;

            return $this->sorting->get($key) ?? $index + 100000;
        });
    }
}
