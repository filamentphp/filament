<?php

namespace Filament\Tests\Admin\Resources;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;

class ResourcesServiceProvider extends PluginServiceProvider
{
    public static string $name = 'resources';

    protected function getResources(): array
    {
        return [
            PostResource::class,
        ];
    }
}
