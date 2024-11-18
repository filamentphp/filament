<?php

namespace Filament\GlobalSearch;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;

class DefaultGlobalSearchProvider implements Contracts\GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = GlobalSearchResults::make();

        foreach (Filament::getResources() as $resource) {
            if (! $resource::canGloballySearch()) {
                continue;
            }

            $resourceResults = ($query) ? $resource::getGlobalSearchResults($query) : Collection::make();

            if (! $resourceResults->count()) {
                continue;
            }

            $builder->category($resource::getPluralModelLabel(), $resourceResults);
        }

        return $builder;
    }
}
