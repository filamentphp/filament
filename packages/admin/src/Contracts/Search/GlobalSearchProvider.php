<?php

declare(strict_types=1);

namespace Filament\Contracts\Search;

use Filament\GlobalSearch\GlobalSearchResults;

interface GlobalSearchProvider
{
    /**
     * Execute the search query globally.
     *
     * @param string $query
     * @return GlobalSearchResults|null
     */
    public function getResults(string $query): ?GlobalSearchResults;
}
