<?php

use Filament\Tests\TestCase;

uses(TestCase::class);

it('can create navigation items', function () {
    $item = \Filament\Navigation\NavigationItem::make()
        ->label('Test')
        ->url('/test')
        ->sort(0)
        ->icon('heroicon-o-home');

    $this->assertEquals($item->getLabel(), 'Test');
    $this->assertEquals($item->getUrl(), '/test');
    $this->assertEquals($item->getSort(), 0);
    $this->assertEquals($item->getIcon(), 'heroicon-o-home');
});

it('can create active nav items', function () {
    $item = \Filament\Navigation\NavigationItem::make()
        ->label('Test')
        ->isActiveWhen(function () {
            return false;
        });
    $this->assertFalse($item->isActive());
    $item->isActiveWhen(function () {
        return true;
    });
    $this->assertTrue($item->isActive());
});
