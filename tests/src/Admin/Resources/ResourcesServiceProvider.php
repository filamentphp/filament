<?php

namespace Filament\Tests\Admin\Resources;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;

class ResourcesServiceProvider extends PluginServiceProvider
{
    public static string $name = 'resources';

    protected function getResources(): array
    {
        return [
            PostResource::class,
            UserResource::class,
        ];
    }
}
