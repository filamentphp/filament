<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class GlobalSearchResults implements Arrayable
{
    protected Collection $categories;

    public function __construct()
    {
        $this->categories = Collection::make();
    }

    public static function make(): self
    {
        return new self();
    }

    /** @param \Filament\GlobalSearch\GlobalSearchResult[] $results */
    public function category(string $name, Arrayable | array $results = []): static
    {
        $this->categories[$name] = Collection::make($results);

        return $this;
    }

    public function toArray()
    {
        return $this->categories->toArray();
    }
}
