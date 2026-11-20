<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationManager;
use Filament\Panel;
use Filament\Tests\Panels\Navigation\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('isolates navigation while switching panels in the same request', function (): void {
    $firstPanel = Panel::make()
        ->id('first-navigation-panel')
        ->navigationItems([
            NavigationItem::make('First panel item')->url('#'),
        ]);
    $secondPanel = Panel::make()
        ->id('second-navigation-panel')
        ->navigationItems([
            NavigationItem::make('Second panel item')->url('#'),
        ]);

    $getLabels = function (Panel $panel): array {
        Filament::setCurrentPanel($panel);

        return collect($panel->getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems())
            ->map(fn (NavigationItem $item): string | Htmlable => $item->getLabel())
            ->all();
    };

    expect($getLabels($firstPanel))->toBe(['First panel item']);

    $navigationManager = app(NavigationManager::class);

    expect($getLabels($secondPanel))->toBe(['Second panel item'])
        ->and($getLabels($firstPanel))->toBe(['First panel item'])
        ->and(app(NavigationManager::class))->toBe($navigationManager);
});
