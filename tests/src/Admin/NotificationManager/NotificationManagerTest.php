<?php

use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\NotificationManager\TestCase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

uses(TestCase::class);

it('adds notifications to the session', function () {
    Livewire::test(Settings::class)
        ->call('notificationManager');

    expect(Session::get('notifications'))
        ->toBeArray()
        ->toHaveLength(1)
        ->sequence(
            fn ($notification) => $notification->message->toBe('Foobar!')
        );
});

it('can automatically dispatch notify event to browser', function () {
    Livewire::test(Settings::class)
        ->call('notificationManager')
        ->assertDispatchedBrowserEvent('notify');
});

it('will not dispatch notify event if Livewire component redirects', function () {
    Livewire::test(Settings::class)
        ->call('notificationManager', redirect: true)
        ->assertNotDispatchedBrowserEvent('notify');
});
