<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelRegistration;

use Filament\Facades\Filament;

function register(array &$events): void
{
    Filament::registerNavigationGroups(groups: groups($events));
    Filament::registerNavigationItems(items: []);
    Filament::registerPages(pages: []);
    Filament::registerResources(resources: []);
    Filament::registerWidgets(widgets: []);
}

function groups(array &$events): array
{
    $events[] = 'groups';

    return ['First', 'Second'];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PanelRegistration;

use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Filament\Facades\Filament;

function register(array &$events): void
{
    (static function (array $groups): void {
        try {
            Filament::getDefaultPanel()->navigationGroups($groups);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups');
        }
    })(groups: groups($events));
    (static function (array $items): void {
        try {
            Filament::getDefaultPanel()->navigationItems($items);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `navigationItems()` method on the panel configuration to register navigation items. See the documentation - https://filamentphp.com/docs/panels/navigation#registering-custom-navigation-items');
        }
    })(items: []);
    (static function (array $pages): void {
        try {
            Filament::getDefaultPanel()->pages($pages);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `pages()` method on the panel configuration to register pages.');
        }
    })(pages: []);
    (static function (array $resources): void {
        try {
            Filament::getDefaultPanel()->resources($resources);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `resources()` method on the panel configuration to register resources.');
        }
    })(resources: []);
    (static function (array $widgets): void {
        try {
            Filament::getDefaultPanel()->widgets($widgets);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `widgets()` method on the panel configuration to register widgets.');
        }
    })(widgets: []);
}

function groups(array &$events): array
{
    $events[] = 'groups';

    return ['First', 'Second'];
}
