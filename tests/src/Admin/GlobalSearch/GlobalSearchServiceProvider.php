<?php

namespace Filament\Tests\Admin\GlobalSearch;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;

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
