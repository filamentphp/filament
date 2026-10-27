<?php

use Filament\Facades\Filament;
use Filament\Livewire\DatabaseNotifications;
use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('configures and disables database notification polling with either setter', function (string $method): void {
    $panel = Filament::getCurrentOrDefaultPanel();
    $notifications = app(DatabaseNotifications::class);

    expect($panel->{$method}(interval: '17s'))->toBe($panel)
        ->and($notifications->getPollingInterval())->toBe('17s');

    $interval = '43s';
    $panel->{$method}(interval: static function () use (&$interval): ?string {
        return $interval;
    });

    expect($notifications->getPollingInterval())->toBe('43s');

    $interval = null;

    expect($notifications->getPollingInterval())->toBeNull()
        ->and($panel->{$method}(interval: null))->toBe($panel)
        ->and($notifications->getPollingInterval())->toBeNull();
})->with(['databaseNotificationsPollingInterval', 'databaseNotificationsPolling']);

it('delegates the legacy polling setter to `databaseNotificationsPollingInterval()` while retaining protected configuration', function (): void {
    $panel = new class extends Panel
    {
        protected string | Closure | null $databaseNotificationsPolling = '11s';

        public function databaseNotificationsPollingInterval(string | Closure | null $interval): static
        {
            return parent::databaseNotificationsPollingInterval($interval ?? '29s');
        }
    };

    expect($panel->getDatabaseNotificationsPollingInterval())->toBe('11s')
        ->and($panel->databaseNotificationsPolling(interval: null))->toBe($panel)
        ->and($panel->getDatabaseNotificationsPollingInterval())->toBe('29s');
});
