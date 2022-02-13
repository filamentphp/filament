<?php

namespace Filament\GlobalSearch;

use Filament\Facades\Filament;

class DefaultGlobalSearchProvider implements Contracts\GlobalSearchProvider
{
    public function getResults(string $query): ?array
    {
        $results = [];

        foreach (Filament::getResources() as $resource) {
            if (! $resource::canGloballySearch()) {
                continue;
            }

            $resourceResults = $resource::getGlobalSearchResults($query);

            if (! $resourceResults->count()) {
                continue;
            }

            $results[$resource::getPluralLabel()] = $resourceResults;
        }

        return $results;
    }
}
