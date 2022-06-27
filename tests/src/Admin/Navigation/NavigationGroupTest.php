<?php

use Filament\Tests\TestCase;

use function Pest\Laravel\get;

uses(TestCase::class);

it('can create navigation groups', function () {
    $group = \Filament\Navigation\NavigationGroup::make()
        ->label('Test')
        ->sort(0)
        ->collapsible(false)
        ->collapsed(false)
        ->icon('heroicon-o-home');

    $this->assertEquals($group->getLabel(), 'Test');
    $this->assertEquals($group->getSort(), 0);
    $this->assertEquals($group->isCollapsible(), false);
    $this->assertEquals($group->isCollapsed(), false);
    $this->assertEquals($group->getIcon(), 'heroicon-o-home');
});

it('can add navigation items to a group', function () {
    $item1 = \Filament\Navigation\NavigationItem::make()->label('1');
    $item2 = \Filament\Navigation\NavigationItem::make()->label('2');
    $item3 = \Filament\Navigation\NavigationItem::make()->label('3');

    $group = \Filament\Navigation\NavigationGroup::make()
        ->item($item1)
        ->items([$item2, $item3]);

    $this->assertEquals($group->getItems(), [$item1, $item2, $item3]);
});
