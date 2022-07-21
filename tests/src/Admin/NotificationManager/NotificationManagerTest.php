<?php

use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\NotificationManager\TestCase;
use Illuminate\Support\Facades\Session;
use Livewire\LivewireManager;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can immediately emit notificationsSent event', function () {
    $component = livewire(Settings::class);

    LivewireManager::$isLivewireRequestTestingOverride = true;

    $component
        ->call('notificationManager')
        ->assertEmitted('notificationsSent');
});

it('will not emit notificationsSent event if Livewire component redirects', function () {
    livewire(Settings::class)
        ->call('notificationManager', redirect: true)
        ->assertNotEmitted('notificationsSent');

    expect(Session::get('filament.notifications'))
        ->toBeArray()
        ->toHaveLength(1)
        ->sequence(
            fn ($notification) => $notification->title->toContain('Saved!')
        );
});
