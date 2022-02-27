<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class GlobalSearchResults
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

    public function category(string $name, Arrayable | array $results = []): static
    {
        $this->categories[$name] = $results;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }
}
