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
            'logout' => MenuItem::make()->label('Exit')->postAction('/exit'),
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

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => fn(Action $action) => $action->label('My profile'),
            Action::make('help-support')->label('Help & support')->color('warning')->url('/help', true)->openUrlInNewTab(false)->sort(+3),
        ],
        [
            Action::make('hidden')->label('Hidden')->hidden()->visible(true),
            'logout' => fn(Action $action) => $action->label('Exit')->url('/exit')->postToUrl(),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => fn(Action $action) => $action->label('Organization')->sort(-1),
        'billing' => fn(Action $action) => $action->label('Plan')->visible(false),
    ]);
}

function legacyRegistration(): void
{
    Filament::getDefaultPanel()->userMenuItems([Action::make('docs')->label('Docs')->url('/docs')]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        Action::make('item-1')->label($label),
        Action::make('item-2')->label(fn () => 'Dynamic'),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
