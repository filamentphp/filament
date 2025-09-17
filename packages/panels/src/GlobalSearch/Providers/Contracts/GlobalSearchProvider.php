<?php

namespace Filament\GlobalSearch\Providers\Contracts;

use Filament\GlobalSearch\GlobalSearchResults;

interface GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults;
}
