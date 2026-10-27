<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelIntegrations;

use Filament\Facades\Filament as PanelFacade;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;

function register(array &$events): array
{
    $hook = function (array $scopes, array $data) use (&$events): string {
        $events[] = [$scopes, $data];

        return 'first';
    };

    $results = [
        PanelFacade::registerRenderHook(hook: $hook, name: 'upgrade.panel'),
        PanelFacade::registerScriptData(data: ['legacy' => 17, 'shared' => 'old']),
    ];

    FilamentView::registerRenderHook('upgrade.panel', fn (): string => 'second');
    FilamentView::registerRenderHook('upgrade.panel', fn (): string => 'scoped', scopes: 'only');
    FilamentAsset::registerScriptData(['shared' => 'new']);
    FilamentAsset::registerScriptData(['package' => 29], package: 'plugin');

    return [...$results, PanelFacade::renderHook(name: 'upgrade.panel')->toHtml()];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelIntegrations;

use Filament\Facades\Filament as PanelFacade;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;

function register(array &$events): array
{
    $hook = function (array $scopes, array $data) use (&$events): string {
        $events[] = [$scopes, $data];

        return 'first';
    };

    $results = [
        FilamentView::registerRenderHook(hook: $hook, name: 'upgrade.panel'),
        FilamentAsset::registerScriptData(data: ['legacy' => 17, 'shared' => 'old']),
    ];

    FilamentView::registerRenderHook('upgrade.panel', fn (): string => 'second');
    FilamentView::registerRenderHook('upgrade.panel', fn (): string => 'scoped', scopes: 'only');
    FilamentAsset::registerScriptData(['shared' => 'new']);
    FilamentAsset::registerScriptData(['package' => 29], package: 'plugin');

    return [...$results, FilamentView::renderHook(name: 'upgrade.panel')->toHtml()];
}
