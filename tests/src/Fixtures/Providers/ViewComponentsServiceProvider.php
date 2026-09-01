<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Tests\Fixtures\View\Components\EntryWrapperComponent;
use Filament\Tests\Fixtures\View\Components\FieldWrapperComponent;
use Illuminate\Support\ServiceProvider;

class ViewComponentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Aliases must be registered in a service provider instead of individual tests, since
        // the `DynamicComponent` tag compiler snapshots the alias registry once per process.
        $this->loadViewComponentsAs('test-plugin', [
            'wrapper' => FieldWrapperComponent::class,
            'entry-wrapper' => EntryWrapperComponent::class,
        ]);
    }
}
