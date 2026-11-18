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

function registerManager(\Filament\FilamentManager $manager, array &$events): array
{
    $stored = manager($manager, $events);

    return [
        $stored->registerNavigationGroups(['Manager']),
        $stored->registerNavigationItems([]),
        $stored->registerPages([]),
        $stored->registerResources([]),
        $stored->registerWidgets([]),
        $stored->registerUserMenuItems([]),
        $stored->registerTheme(new \Illuminate\Support\HtmlString('theme')),
        $stored->registerViteTheme(buildDirectory: argument('build', $events), theme: argument('theme', $events)),
        $stored->registerRenderHook(hook: hook($events), name: argument('manager.hook', $events)),
        $stored->renderHook('manager.hook')->toHtml(),
        $stored->registerScriptData(['manager' => 31]),
        filament()->registerScriptData(['helper' => 37]),
    ];
}

function nullableManager(?\Filament\FilamentManager $manager, array &$events): ?string
{
    return manager($manager, $events)?->renderHook(argument('manager.hook', $events))->toHtml();
}

function manager(?\Filament\FilamentManager $manager, array &$events): ?\Filament\FilamentManager
{
    $events[] = 'receiver';

    return $manager;
}

function argument(string $value, array &$events): string
{
    $events[] = $value;

    return $value;
}

function hook(array &$events): \Closure
{
    $events[] = 'hook-created';

    return static function () use (&$events): string {
        $events[] = 'hook-rendered';

        return 'manager';
    };
}

class InheritedManager extends \Filament\FilamentManager {}

function inheritedManager(InheritedManager $manager): void
{
    $manager->registerScriptData(['inherited' => 41]);
}

class CustomManager extends InheritedManager
{
    public array $groups = [];

    public function registerNavigationGroups(array $groups): void
    {
        $this->groups = $groups;
    }
}

function customManager(CustomManager $manager): void
{
    $manager->registerNavigationGroups(['Custom']);
}

class PanelManager extends InheritedManager
{
    public function __construct(public \Filament\Panel $panel) {}

    public function getDefaultPanel(): \Filament\Panel
    {
        return $this->panel;
    }
}

class ManagerHolder
{
    public function __construct(public ?\Filament\FilamentManager $manager) {}
}

function chainedManager(?ManagerHolder $holder, array &$events): array
{
    $filamentUpgradeReceiver0 = 'original';

    return [$holder?->manager->renderHook(argument('manager.hook', $events))->toHtml(), $filamentUpgradeReceiver0];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelIntegrations;

use Filament\FilamentManager;
use Illuminate\Support\HtmlString;
use Closure;
use Filament\Panel;
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

function registerManager(FilamentManager $manager, array &$events): array
{
    $stored = manager($manager, $events);

    return [
        $stored->getDefaultPanel()->navigationGroups(['Manager']),
        $stored->getDefaultPanel()->navigationItems([]),
        $stored->getDefaultPanel()->pages([]),
        $stored->getDefaultPanel()->resources([]),
        $stored->getDefaultPanel()->widgets([]),
        $stored->getDefaultPanel()->userMenuItems([]),
        $stored->getDefaultPanel()->theme(new HtmlString('theme')),
        $stored->getDefaultPanel()->viteTheme(buildDirectory: argument('build', $events), theme: argument('theme', $events)),
        FilamentView::registerRenderHook(hook: hook($events), name: argument('manager.hook', $events)),
        FilamentView::renderHook('manager.hook')->toHtml(),
        FilamentAsset::registerScriptData(['manager' => 31]),
        FilamentAsset::registerScriptData(['helper' => 37]),
    ];
}

function nullableManager(?FilamentManager $manager, array &$events): ?string
{
    return manager($manager, $events)?->renderHook(argument('manager.hook', $events))->toHtml();
}

function manager(?FilamentManager $manager, array &$events): ?FilamentManager
{
    $events[] = 'receiver';

    return $manager;
}

function argument(string $value, array &$events): string
{
    $events[] = $value;

    return $value;
}

function hook(array &$events): Closure
{
    $events[] = 'hook-created';

    return static function () use (&$events): string {
        $events[] = 'hook-rendered';

        return 'manager';
    };
}

class InheritedManager extends FilamentManager {}

function inheritedManager(InheritedManager $manager): void
{
    FilamentAsset::registerScriptData(['inherited' => 41]);
}

class CustomManager extends InheritedManager
{
    public array $groups = [];

    public function registerNavigationGroups(array $groups): void
    {
        $this->groups = $groups;
    }
}

function customManager(CustomManager $manager): void
{
    $manager->registerNavigationGroups(['Custom']);
}

class PanelManager extends InheritedManager
{
    public function __construct(public Panel $panel) {}

    public function getDefaultPanel(): Panel
    {
        return $this->panel;
    }
}

class ManagerHolder
{
    public function __construct(public ?FilamentManager $manager) {}
}

function chainedManager(?ManagerHolder $holder, array &$events): array
{
    $filamentUpgradeReceiver0 = 'original';

    return [$holder?->manager->renderHook(argument('manager.hook', $events))->toHtml(), $filamentUpgradeReceiver0];
}
