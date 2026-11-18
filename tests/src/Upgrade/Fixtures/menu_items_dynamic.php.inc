<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItemsDynamic;

use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel, array &$events, bool &$hidden, string &$label): void
{
    $eager = static function (string $value) use (&$events): string {
        $events[] = $value;

        return $value;
    };
    $panel->userMenuItems([
        'profile' => MenuItem::make()
            ->label(static function () use (&$events, &$label): string {
                $events[] = 'label';

                return $label;
            })
            ->postAction('/post')
            ->openUrlInNewTab()
            ->hidden(static function () use (&$events, &$hidden): bool {
                $events[] = 'hidden';

                return $hidden;
            }),
        MenuItem::make()->label($eager('Help'))->url('/help')->sort(static fn (): int => -3),
    ]);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItemsDynamic;

use Filament\Actions\Action;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel, array &$events, bool &$hidden, string &$label): void
{
    $eager = static function (string $value) use (&$events): string {
        $events[] = $value;

        return $value;
    };
    $panel->userMenuItems([
        'profile' => function (Action $action) use (&$events, &$hidden, &$label) {
            return $action->label(static function () use (&$events, &$label): string {
                $events[] = 'label';

                return $label;
            })->url('/post')->postToUrl()->openUrlInNewTab()->hidden(static function () use (&$events, &$hidden): bool {
                $events[] = 'hidden';

                return $hidden;
            });
        },
        Action::make('item-2')->label($eager('Help'))->url('/help')->sort(static fn (): int => -3),
    ]);
}
