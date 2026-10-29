<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItems;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => MenuItem::make()->label('My profile'),
            MenuItem::make()->label('Help & support')->color('warning')->url('/help', true)->openUrlInNewTab(false)->sort(+3),
        ],
        [
            MenuItem::make()->label('Hidden')->hidden()->visible(true),
            'logout' => MenuItem::make()->label('Exit')->postAction('/exit')->url(''),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => MenuItem::make()->label('Organization')->sort(-1),
        'billing' => MenuItem::make()->label('Plan')->visible(false),
    ]);
}

function legacyRegistration(): void
{
    Filament::registerUserMenuItems([MenuItem::make()->label('Docs')->url('/docs')]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        MenuItem::make()->label($label),
        MenuItem::make()->label(fn () => 'Dynamic'),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItems;

use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => MenuItem::make()->label('My profile'),
            MenuItem::make()->label('Help & support')->color('warning')->url('/help', true)->openUrlInNewTab(false)->sort(+3),
        ],
        [
            MenuItem::make()->label('Hidden')->hidden()->visible(true),
            'logout' => MenuItem::make()->label('Exit')->postAction('/exit')->url(''),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => MenuItem::make()->label('Organization')->sort(-1),
        'billing' => MenuItem::make()->label('Plan')->visible(false),
    ]);
}

function legacyRegistration(): void
{
    (static function (array $items): void {
        try {
            Filament::getDefaultPanel()->userMenuItems($items);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `userMenuItems()` method on the panel configuration to register user menu items. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-the-user-menu');
        }
    })([MenuItem::make()->label('Docs')->url('/docs')]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        MenuItem::make()->label($label),
        MenuItem::make()->label(fn () => 'Dynamic'),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
