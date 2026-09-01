<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Pages\Actions;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can rate limit an action after the configured number of attempts', function (): void {
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
});

it('persists rate limit across separate `Livewire` component instances', function (): void {
    // Exhaust the rate limit
    livewire(Actions::class)
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited');

    // New component instance should still be rate limited
    livewire(Actions::class)
        ->callAction('rate-limited')
        ->assertNotDispatched('rate-limited-called')
        ->assertNotified('Too many attempts');
});

it('resets rate limit when cache is cleared', function (): void {
    // Exhaust the rate limit
    livewire(Actions::class)
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->callAction('rate-limited')
        ->assertNotDispatched('rate-limited-called')
        ->assertNotified('Too many attempts');

    cache()->clear();

    // Should work again after cache clear
    livewire(Actions::class)
        ->callAction('rate-limited')
        ->assertDispatched('rate-limited-called')
        ->assertNotNotified('Too many attempts');
});
