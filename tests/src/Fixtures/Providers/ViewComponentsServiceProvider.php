<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\Tests\Fixtures\View\Components\EntryWrapperComponent;
use Filament\Tests\Fixtures\View\Components\FieldWrapperComponent;
use Filament\View\PanelsRenderHook;
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

        // Audit settled colors and visibility, not intermediate animation frames.
        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_END, static fn (): string => '<style>*, *::before, *::after { transition: none !important; animation: none !important; }</style>');
    }
}
