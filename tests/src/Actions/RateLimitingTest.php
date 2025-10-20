<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Pages\Actions;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can rate limit an action', function (): void {
    livewire(Actions::class)
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts')
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts')
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts')
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts')
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts')
        ->callAction('rate-limited')
        ->assertNotDispatched('rate-limited-called')
        ->assertNotified('Too many attempts');

    livewire(Actions::class)
        ->callAction('rate-limited')
        ->assertNotDispatched('rate-limited-called')
        ->assertNotified('Too many attempts');

    cache()->clear();

    livewire(Actions::class)
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts');
});
