<?php

namespace Filament\Notifications\Testing;

use Closure;
use Filament\Notifications\Notification;
use Filament\Support\Actions\Action as BaseAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Testing\TestableLivewire;
use Pest\Expectation;

/**
 * @method Component instance()
 *
 * @mixin TestableLivewire
 */
class TestsNotifications
{
    public function assertNotified(): Closure
    {
        return function (Notification | string $notification = null): static {
            $notifications = session()->get('filament.notifications');

            expect($notifications)
                ->toBeArray();

            $expectedNotification = Arr::last($notifications);

            expect($expectedNotification)
                ->toBeArray();

            if ($notification instanceof Notification) {
                expect($expectedNotification)
                    ->toBe($notification->toArray());
            } elseif (filled($notification)) {
                /** @phpstan-ignore-next-line */
                expect($expectedNotification)
                    ->title->toBe($notification);
            }

            return $this;
        };
    }
}
