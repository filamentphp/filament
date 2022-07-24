<?php

namespace Filament\Tests\Admin\Navigation;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Fixtures\Resources\ProductResource;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;

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
