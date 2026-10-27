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

use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => static fn(Action $action): Action => $action->color(null)->icon(null)->label('My profile')->postToUrl(false)->sort(0)->visible(true)->url(null, false),
            static fn(): Action => Action::make(Str::slug(Str::transliterate('Help & support', strict: true)))->color('warning')->icon(null)->label('Help & support')->postToUrl(false)->sort(3)->visible(true)->url('/help', false),
        ],
        [
            static fn(): Action => Action::make(Str::slug(Str::transliterate('Hidden', strict: true)))->color(null)->icon(null)->label('Hidden')->postToUrl(false)->sort(0)->visible(false)->url(null, false),
            'logout' => static fn(Action $action): Action => $action->color(null)->icon(null)->label('Exit')->postToUrl(true)->sort(0)->visible(true)->url('', false),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => static fn(Action $action): Action => $action->color(null)->icon(null)->label('Organization')->postToUrl(false)->sort(-1)->visible(true)->url(null, false),
        'billing' => static fn(Action $action): Action => $action->color(null)->icon(null)->label('Plan')->postToUrl(false)->sort(0)->visible(false)->url(null, false),
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
    })([static fn(): Action => Action::make(Str::slug(Str::transliterate('Docs', strict: true)))->color(null)->icon(null)->label('Docs')->postToUrl(false)->sort(0)->visible(true)->url('/docs', false)]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        MenuItem::make()->label($label),
        MenuItem::make()->label(fn () => 'Dynamic'),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
