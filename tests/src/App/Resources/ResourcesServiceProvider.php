<?php

namespace Filament\Tests\App\Resources;

use Filament\PluginServiceProvider;
use Filament\Tests\App\Fixtures\Resources\PostResource;
use Filament\Tests\App\Fixtures\Resources\UserResource;

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
