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

use Filament\Facades\Filament;

function register(array &$events): void
{
    Filament::getDefaultPanel()->navigationGroups(groups: groups($events));
    Filament::getDefaultPanel()->navigationItems(items: []);
    Filament::getDefaultPanel()->pages(pages: []);
    Filament::getDefaultPanel()->resources(resources: []);
    Filament::getDefaultPanel()->widgets(widgets: []);
}

function groups(array &$events): array
{
    $events[] = 'groups';

    return ['First', 'Second'];
}
