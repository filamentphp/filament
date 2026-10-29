<?php

use Filament\Facades\Filament;
use Filament\Livewire\DatabaseNotifications;
use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('configures and disables database notification polling', function (): void {
    $panel = Filament::getCurrentOrDefaultPanel();
    $notifications = app(DatabaseNotifications::class);

    expect($panel->databaseNotificationsPollingInterval(interval: '17s'))->toBe($panel)
        ->and($notifications->getPollingInterval())->toBe('17s');

    $interval = '43s';
    $panel->databaseNotificationsPollingInterval(interval: static function () use (&$interval): ?string {
        return $interval;
    });

    expect($notifications->getPollingInterval())->toBe('43s');

    $interval = null;

    expect($notifications->getPollingInterval())->toBeNull()
        ->and($panel->databaseNotificationsPollingInterval(interval: null))->toBe($panel)
        ->and($notifications->getPollingInterval())->toBeNull();
});

it('supports polling setter overrides and protected interval configuration', function (): void {
    $panel = new class extends Panel
    {
        protected string | Closure | null $databaseNotificationsPollingInterval = '11s';

        public function databaseNotificationsPollingInterval(string | Closure | null $interval): static
        {
            return parent::databaseNotificationsPollingInterval($interval ?? '29s');
        }
    };

    expect($panel->getDatabaseNotificationsPollingInterval())->toBe('11s')
        ->and($panel->databaseNotificationsPollingInterval(interval: null))->toBe($panel)
        ->and($panel->getDatabaseNotificationsPollingInterval())->toBe('29s');
});
