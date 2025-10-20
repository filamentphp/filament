<?php

use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can configure stackable notifications', function () {
    $panel = Panel::make()
        ->id('test')
        ->stackableNotifications(false);

    expect($panel->hasStackableNotifications())->toBeFalse();
});

it('defaults to stackable notifications enabled', function () {
    $panel = Panel::make()
        ->id('test');

    expect($panel->hasStackableNotifications())->toBeTrue();
});

it('can configure stackable notifications with closure', function () {
    $panel = Panel::make()
        ->id('test')
        ->stackableNotifications(fn () => false);

    expect($panel->hasStackableNotifications())->toBeFalse();
});
