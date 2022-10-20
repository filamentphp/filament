<?php

namespace Filament\Tests\App\GlobalSearch;

use Filament\Support\PluginServiceProvider;
use Filament\Tests\App\Fixtures\Resources\PostResource;

class GlobalSearchServiceProvider extends PluginServiceProvider
{
    public static string $name = 'global-search';

    protected function getResources(): array
    {
        return [
            PostResource::class,
        ];
    }
}
