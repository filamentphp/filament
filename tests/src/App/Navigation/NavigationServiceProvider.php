<?php

namespace Filament\Tests\App\Navigation;

use Filament\Support\PluginServiceProvider;
use Filament\Tests\App\Fixtures\Pages\Settings;
use Filament\Tests\App\Fixtures\Resources\PostCategoryResource;
use Filament\Tests\App\Fixtures\Resources\PostResource;
use Filament\Tests\App\Fixtures\Resources\ProductResource;
use Filament\Tests\App\Fixtures\Resources\UserResource;

class NavigationServiceProvider extends PluginServiceProvider
{
    public static string $name = 'navigation';

    protected function getResources(): array
    {
        return [
            PostResource::class,
            PostCategoryResource::class,
            ProductResource::class,
            UserResource::class,
        ];
    }

    protected function getPages(): array
    {
        return [
            Settings::class,
        ];
    }
}
