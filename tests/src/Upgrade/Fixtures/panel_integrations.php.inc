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
        manager($stored, $events)->registerRenderHook(hook: hook($events), name: argument('manager.hook', $events)),
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
use Closure;
use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Filament\Panel;
use Error;
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
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerNavigationGroups') ? Closure::fromCallable([$manager, 'registerNavigationGroups']) : static function (array $groups) use ($manager): void {
            try {
                $manager->getDefaultPanel()->navigationGroups($groups);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups');
            }
        }))($stored)(['Manager']),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerNavigationItems') ? Closure::fromCallable([$manager, 'registerNavigationItems']) : static function (array $items) use ($manager): void {
            try {
                $manager->getDefaultPanel()->navigationItems($items);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `navigationItems()` method on the panel configuration to register navigation items. See the documentation - https://filamentphp.com/docs/panels/navigation#registering-custom-navigation-items');
            }
        }))($stored)([]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerPages') ? Closure::fromCallable([$manager, 'registerPages']) : static function (array $pages) use ($manager): void {
            try {
                $manager->getDefaultPanel()->pages($pages);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `pages()` method on the panel configuration to register pages.');
            }
        }))($stored)([]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerResources') ? Closure::fromCallable([$manager, 'registerResources']) : static function (array $resources) use ($manager): void {
            try {
                $manager->getDefaultPanel()->resources($resources);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `resources()` method on the panel configuration to register resources.');
            }
        }))($stored)([]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerWidgets') ? Closure::fromCallable([$manager, 'registerWidgets']) : static function (array $widgets) use ($manager): void {
            try {
                $manager->getDefaultPanel()->widgets($widgets);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `widgets()` method on the panel configuration to register widgets.');
            }
        }))($stored)([]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerUserMenuItems') ? Closure::fromCallable([$manager, 'registerUserMenuItems']) : static function (array $items) use ($manager): void {
            try {
                $manager->getDefaultPanel()->userMenuItems($items);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `userMenuItems()` method on the panel configuration to register user menu items. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-the-user-menu');
            }
        }))($stored)([]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerTheme') ? Closure::fromCallable([$manager, 'registerTheme']) : static function (string|Htmlable|null $theme) use ($manager): void {
            try {
                $manager->getDefaultPanel()->theme($theme);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `theme()` method on the panel configuration to register themes.');
            }
        }))($stored)(new HtmlString('theme')),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerViteTheme') ? Closure::fromCallable([$manager, 'registerViteTheme']) : static function (string|array $theme, ?string $buildDirectory = null) use ($manager): void {
            try {
                $manager->getDefaultPanel()->viteTheme($theme, $buildDirectory);
            } catch (NoDefaultPanelSetException $exception) {
                throw new LogicException('Please use the `viteTheme()` method on the panel configuration to register themes.');
            }
        }))($stored)(buildDirectory: argument('build', $events), theme: argument('theme', $events)),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerRenderHook') ? Closure::fromCallable([$manager, 'registerRenderHook']) : static function (string $name, Closure $hook) use ($manager): void {
            FilamentView::registerRenderHook($name, $hook);
        }))(manager($stored, $events))(hook: hook($events), name: argument('manager.hook', $events)),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'renderHook') ? Closure::fromCallable([$manager, 'renderHook']) : static function (string $name) use ($manager): Htmlable {
            return FilamentView::renderHook($name);
        }))($stored)('manager.hook')->toHtml(),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerScriptData') ? Closure::fromCallable([$manager, 'registerScriptData']) : static function (array $data) use ($manager): void {
            FilamentAsset::registerScriptData($data);
        }))($stored)(['manager' => 31]),
        (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerScriptData') ? Closure::fromCallable([$manager, 'registerScriptData']) : static function (array $data) use ($manager): void {
            FilamentAsset::registerScriptData($data);
        }))(filament())(['helper' => 37]),
    ];
}

function nullableManager(?FilamentManager $manager, array &$events): ?string
{
    return (static fn(?FilamentManager $manager) => $manager === null ? null : (static fn(callable $callback) => static fn(...$arguments) => $callback(...$arguments))(\method_exists($manager, 'renderHook') ? Closure::fromCallable([$manager, 'renderHook']) : static function (string $name) use ($manager): Htmlable {
        return FilamentView::renderHook($name);
    }))(manager($manager, $events))?->__invoke(argument('manager.hook', $events))->toHtml();
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
    (static fn(?FilamentManager $manager) => $manager === null ? null : (\method_exists($manager, 'registerScriptData') ? Closure::fromCallable([$manager, 'registerScriptData']) : static function (array $data) use ($manager): void {
        FilamentAsset::registerScriptData($data);
    }))($manager)(['inherited' => 41]);
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

    return [(($filamentUpgradeReceiver0Value = $holder) === null ? null : (static fn(?FilamentManager $manager) => $manager === null ? throw new Error('Call to a member function renderHook() on null') : (static fn(callable $callback) => static fn(...$arguments) => $callback(...$arguments))(\method_exists($manager, 'renderHook') ? Closure::fromCallable([$manager, 'renderHook']) : static function (string $name) use ($manager): Htmlable {
        return FilamentView::renderHook($name);
    }))($filamentUpgradeReceiver0Value->manager))?->__invoke(argument('manager.hook', $events))->toHtml(), $filamentUpgradeReceiver0];
}
