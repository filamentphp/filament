<?php

namespace Filament\Tests\Admin\GlobalSearch;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;

class LimitedGlobalSearchServiceProvider extends PluginServiceProvider
{
    public static string $name = 'limited-global-search';

    protected function getResources(): array
    {
        return [
            LimitedPostResource::class,
        ];
    }
}

class LimitedPostResource extends PostResource
{
    protected static int $globalSearchResultsLimit = 1;
}
