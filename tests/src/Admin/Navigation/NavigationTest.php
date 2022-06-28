<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UnlabelledNavigationGroup;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('builds the default navigation', function () {
    $nav = Filament::getNavigation();

    expect($nav)->toHaveCount(1)
        ->and($nav[0])->toBeInstanceOf(UnlabelledNavigationGroup::class)
        ->and($nav[0]->getLabel())->toBe('')
        ->and($nav[0]->isCollapsible())->toBe(false)
        ->and($nav[0]->isCollapsed())->toBe(false)
        ->and($nav[0]->getItems()[0]->getLabel())->toBe('Dashboard');
});

it('can register groups as array of strings', function () {
    Filament::registerNavigationGroups([
        'Group 1',
        'Group 2',
        'Group 3',
    ]);
    Filament::mountNavigation();
    $registeredGroups = Filament::getNavigationGroups();

    expect($registeredGroups)->toHaveCount(4);
    foreach ($registeredGroups as $group) {
        expect($group)->toBeInstanceOf(NavigationGroup::class);
    }
});

it('can register groups as array of objects', function () {
    Filament::registerNavigationGroups([
        NavigationGroup::make()->label('Group 1'),
        NavigationGroup::make()->label('Group 2'),
        NavigationGroup::make()->label('Group 3'),
    ]);
    Filament::mountNavigation();
    $registeredGroups = Filament::getNavigationGroups();

    expect($registeredGroups)->toHaveCount(4);
    foreach ($registeredGroups as $group) {
        expect($group)->toBeInstanceOf(NavigationGroup::class);
    }
    $nav = Filament::getNavigation();
    expect($nav[0]->getLabel())->toBeEmpty()
        ->and($nav[0]->getItems())->toHaveCount(1)
        ->and($nav[1]->getLabel())->toBe('Group 1')
        ->and($nav[2]->getLabel())->toBe('Group 2')
        ->and($nav[3]->getLabel())->toBe('Group 3');
});

/**
 * @return void
 */
function expectRegisteredNav(): void
{
    $nav = Filament::getNavigation();

    expect($nav)->toHaveCount(4);
    foreach ($nav as $group) {
        expect($group)->toBeInstanceOf(NavigationGroup::class);
    }
    expect($nav[0]->getLabel())->toBeEmpty()
        ->and($nav[0]->getItems())->toBeEmpty()
        ->and($nav[1]->getLabel())->toBe('Group 1')
        ->and($nav[1]->getItems())->toHaveCount(1)
        ->and($nav[2]->getLabel())->toBe('Group 2')
        ->and($nav[2]->getItems())->toHaveCount(1)
        ->and($nav[3]->getLabel())->toBe('Group 3')
        ->and($nav[3]->getItems())->toHaveCount(1);
}

it('can build a manual nav using groups as strings', function () {
    Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
        $builder->group('Group 1', [
            NavigationItem::make()->label('Settings'),
        ]);

        $builder->group('Group 2', [
            NavigationItem::make()->label('Posts'),
        ]);

        $builder->group('Group 3', [
            NavigationItem::make()->label('Users'),
        ]);

        return $builder;
    });

    expectRegisteredNav();
});

it('can build a manual nav using groups objects', function () {
    Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
        $builder->group(NavigationGroup::make()->label('Group 1')
            ->items([
                NavigationItem::make()->label('Settings'),
            ]));

        $builder->group(NavigationGroup::make()->label('Group 2')
            ->items([
                NavigationItem::make()->label('Posts'),
            ]));

        $builder->group(NavigationGroup::make()->label('Group 3')
            ->items([
                NavigationItem::make()->label('Users'),
            ]));

        return $builder;
    });

    expectRegisteredNav();
});
