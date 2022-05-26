<?php

use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\NotificationManager\TestCase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

uses(TestCase::class);

it('can immediately dispatch notify event to browser', function () {
    Livewire::test(Settings::class)
        ->call('notificationManager')
        ->assertDispatchedBrowserEvent('notify');

    expect(Session::get('notifications'))->toBeEmpty();
});

it('will not dispatch notify event if Livewire component redirects', function () {
    Livewire::test(Settings::class)
        ->call('notificationManager', redirect: true)
        ->assertNotDispatchedBrowserEvent('notify');

    expect(Session::get('notifications'))
        ->toBeArray()
        ->toHaveLength(1)
        ->sequence(
            fn ($notification) => $notification->message->toBe('Saved!')
        );
});
